package tostaobank.controller;

import java.time.ZonedDateTime;
import java.util.List;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.server.ResponseStatusException;

import tostaobank.model.Historico;
import tostaobank.model.Investimento;
import tostaobank.model.Usuario;
import tostaobank.repository.HistoricoRepository;
import tostaobank.repository.InvestimentoRepository;
import tostaobank.repository.UsuarioRepository;
import tostaobank.service.CachingService;

@RestController
@RequestMapping(value = "/investimentos")
public class InvestimentoController {
	
	@Autowired 
	private InvestimentoRepository repI;
	@Autowired
	private UsuarioRepository repU;
	@Autowired
	private HistoricoRepository repH;
	@Autowired
	private CachingService cacheS;
	
	//retorna todos os investimentos da pessoa
	@GetMapping(value ="/investimento")
	public List<Investimento> retornaInvestimentos(@RequestParam String email){
		return cacheS.retornaInvestimentos(email); 
	}
	
	@PostMapping(value = "/investir")
	public Investimento investir(@RequestBody Investimento investimento) {
		//cria as variaveis pra poder fazer a busca
		String email = investimento.getEmailUsuario();
		String tipo = investimento.getTipoInvestimento();
		//faz as buscas
		Optional<Investimento> op = repI.findByEmailUsuarioAndTipoInvestimento(email, tipo);
		Optional<Usuario> po = repU.findByEmailUsuario(email);
		//verifica se o usuario tem saldo suficiente
		if (po.isPresent()) {
			Usuario user = po.get();
			if(user.getSaldoUsuario() < investimento.getValorInvestimento() ||  investimento.getValorInvestimento() <=  0) {
				throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Saldo insuficiente");
			}
		//se sim já altera o saldo do usuario e salva
			user.setSaldoUsuario(user.getSaldoUsuario() - investimento.getValorInvestimento());
			repU.save(user);
		//caso haja algum registro de email do usuario e tipo de investimento especificado
		//apenas altera o dado já existente, se não cria novo registro
			if (op.isPresent()) {
				investimento.setDataInvestimento(ZonedDateTime.now());
				Investimento antigo = op.get();
				antigo.setValorInvestimento(antigo.getValorInvestimento() + investimento.getValorInvestimento());
				
				Historico transacao = new Historico();
				transacao.setEmailTransferencia(email);
				transacao.setValorTransferencia(investimento.getValorInvestimento());
				transacao.setTipoTransferencia(tipo);
				transacao.setDataTransferencia(ZonedDateTime.now());
				transacao.setDescTransferencia("Aplicação em investimentos");
				repH.save(transacao);
				cacheS.apagarCache();
				return repI.save(antigo);
			} else {
				investimento.setDataInvestimento(ZonedDateTime.now());
				Historico transacao = new Historico();
				transacao.setEmailTransferencia(email);
				transacao.setValorTransferencia(investimento.getValorInvestimento());
				transacao.setTipoTransferencia(tipo);
				transacao.setDataTransferencia(ZonedDateTime.now());
				transacao.setDescTransferencia("Aplicação em investimentos");
				repH.save(transacao);
				cacheS.apagarCache();
				return repI.save(investimento);
			}
		}
		throw new ResponseStatusException(HttpStatus.NOT_FOUND, "Usuário não encontrado");
	}
	
	@PostMapping(value = "/retirar")
	public Investimento retirar (@RequestBody Investimento investimento) {
		//variaveis pra busca
		String email = investimento.getEmailUsuario();
		String tipo = investimento.getTipoInvestimento();
		//lista para busca
		Optional<Investimento> op = repI.findByEmailUsuarioAndTipoInvestimento(email, tipo);
		Optional<Usuario> po = repU.findByEmailUsuario(email);
		
		//se tiver algo na lista de investimento
		if(op.isPresent()) {
			Investimento inve = op.get();
			Usuario user = po.get();
		//verifica se o valor que se quer retirar é maior que o dinheiro guardado ou menor/igual a zero
			if (inve.getValorInvestimento()< investimento.getValorInvestimento() ||  investimento.getValorInvestimento() <=  0) {
				throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Valor inválido");
			}
		//altera os valores do saldo usuario e tabela investimento
			inve.setValorInvestimento(inve.getValorInvestimento()- investimento.getValorInvestimento());
			repI.save(inve);

			user.setSaldoUsuario(user.getSaldoUsuario()+ investimento.getValorInvestimento());
			repU.save(user);

		//caso não sobre nada no investimento o registro é deletado
			Double valorInvestido = inve.getValorInvestimento();
			if(valorInvestido <= 0) {
				repI.delete(inve);
			}			
			
		//salva a transação no historico
			Historico transacao = new Historico();
			transacao.setEmailTransferencia(email);
			transacao.setValorTransferencia(investimento.getValorInvestimento());
			transacao.setTipoTransferencia(tipo);
			transacao.setDataTransferencia(ZonedDateTime.now());
			transacao.setDescTransferencia("Retirada de rendimentos");
			repH.save(transacao);
			cacheS.apagarCache();
			return inve;
		}
		throw new ResponseStatusException(HttpStatus.NOT_FOUND, "Investimento Inexistente");
		}
	
	
	}

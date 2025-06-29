package tostaobank.controller;

import java.security.SecureRandom;
import java.time.ZonedDateTime;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.server.ResponseStatusException;

import jakarta.validation.Valid;
import tostaobank.DTO.PixDto;
import tostaobank.DTO.RecargaDto;
import tostaobank.model.Cartoes;
import tostaobank.model.Historico;
import tostaobank.model.Usuario;
import tostaobank.repository.CartoesRepository;
import tostaobank.repository.HistoricoRepository;
import tostaobank.repository.UsuarioRepository;
import tostaobank.service.CachingService;
import tostaobank.service.UsuarioService;

@RestController
@RequestMapping(value = "/tostao")
public class UsuarioController {
	
	@Autowired
	private CachingService cachingService;	
	@Autowired
	private UsuarioRepository repU;
	@Autowired
	private HistoricoRepository repH;
	@Autowired
	private CartoesRepository repC;
	@Autowired
	private UsuarioService usuarioService;
	
	public static String geraCartao() {
		SecureRandom random = new SecureRandom();
		StringBuilder sb = new StringBuilder();
		
		sb.append(random.nextInt(9) + 1);
		for (int i = 0;i<15;i++) {
			sb.append(random.nextInt(10));
			}
		String b = sb.toString();
		return b;		
	}
	
	//Login
	@PostMapping(value = "/login")
	public Usuario login(@RequestBody Usuario user){
		Optional<Usuario> usuarioOpt = repU.findByEmailUsuario(user.getEmailUsuario());		
        if (usuarioOpt.isPresent()) {
            Usuario usuario = usuarioOpt.get();		
				if (usuario.getSenhaUsuario().equals(user.getSenhaUsuario())) {
					return usuario;
				}else{
					throw new ResponseStatusException(HttpStatus.UNAUTHORIZED, "Saldo insuficiente");
				}
        }else{
				throw new ResponseStatusException(HttpStatus.NOT_FOUND, "Usuário não Encontrado!");
			}
        }
	
	@GetMapping("/usuario/{id}")
	public ResponseEntity<Usuario> buscarPorId(@PathVariable Long id) {
	    Usuario usuario = usuarioService.buscarPorId(id);
	    return ResponseEntity.ok(usuario);
	}

	
	// recargaCelular, revisar
	@PostMapping(value = "/recarga")
	public Usuario recarga(@RequestBody RecargaDto recarga){
		String email = recarga.getEmail();
		double valor = recarga.getValor();
		String telefone = recarga.getTelefone();
		Optional<Usuario> op = repU.findByEmailUsuario(email);
		//verifica se o usuario existe
		if (op.isPresent()) {
			Usuario user = op.get();	
		// se o saldo do usuario for menor que o valor da recarga desejada lança um erro
			if (user.getSaldoUsuario() < valor) {
				throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Saldo insuficiente");
			}
		// salva alteração no saldo e em seguida registra no histórico
			user.setSaldoUsuario(user.getSaldoUsuario() - valor);			
			repU.save(user);
			Historico transacao = new Historico();
	        transacao.setEmailTransferencia(email);
	        transacao.setValorTransferencia(valor);
	        transacao.setTipoTransferencia("Recarga telefone");
	        transacao.setDataTransferencia(ZonedDateTime.now());
	        transacao.setTelefoneTransferencia(telefone);
	        repH.save(transacao);
	        cachingService.apagarCache();
			return user;
		}
		throw new ResponseStatusException(HttpStatus.NOT_FOUND, "Usuario nao encontrado");
	}
	
	
	//Transferencia pix, revisar
	@PutMapping(value = "/pix")
	public Usuario pix(@Valid @RequestBody PixDto pix) {
		String email = pix.getEmail();
		Double valor = pix.getValor();
		String emailTransferencia = pix.getEmailRecebe();
		// busca o usuario que quer realizar a transferencia e que vai receber
		Optional<Usuario> op = repU.findByEmailUsuario(email);
		Optional<Usuario> po = repU.findByEmailUsuario(emailTransferencia);
		// verifica existencia dos dois usuarios, em positivo, "cria" objetos para puxar e manipular os dados
		if (op.isPresent() && po.isPresent()) {
			Usuario user = op.get();
			Usuario userRecebe = po.get();
		// verifica o saldo do usuario que quer realizar a transferencia
			if (user.getSaldoUsuario() < valor) {
				throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Saldo insuficiente");
			}
		// altera os saldos dos 2 usuarios e salva, após isso salva no historico
			user.setSaldoUsuario(user.getSaldoUsuario() - valor);
			userRecebe.setSaldoUsuario(userRecebe.getSaldoUsuario() + valor);
			repU.save(user);
			repU.save(userRecebe);
			Historico transacao = new Historico();
	        transacao.setEmailTransferencia(email);
	        transacao.setValorTransferencia(valor);
	        transacao.setTipoTransferencia("Pix");
	        transacao.setEmailRecebeTransferencia(emailTransferencia);
	        transacao.setDataTransferencia(ZonedDateTime.now());
	        repH.save(transacao);
        // apaga cache do histórico
	        cachingService.apagarCache();
	        return user;
		}
		throw new ResponseStatusException(HttpStatus.NOT_FOUND, "Usuario nao encontrado");
	}

	// Cadastro e retorno, mexer pra adicionar segurança se tiver tempo
	@PostMapping(value = "/cadastro")
	public Usuario cadastro(@RequestBody Usuario novoUsuario) {
		String email = novoUsuario.getEmailUsuario();
		Optional<Usuario> op = repU.findByEmailUsuario(email);
		if (op.isPresent()){
			throw new ResponseStatusException(HttpStatus.CONFLICT,"usuario já existe");
		}
		String novoCartao = geraCartao();

		novoUsuario.setCartaoUsuario(novoCartao);
		repU.save(novoUsuario);	
		
		Cartoes cartao = new Cartoes();
		cartao.setNumCartoes(novoCartao);
		cartao.setUsuario(novoUsuario);
		repC.save(cartao);
		cachingService.apagarCache();
		return novoUsuario;
	}
	
	@GetMapping(value = "/usuario")
	public Usuario retornaUsuario(@RequestParam String email){
		Optional<Usuario> op = repU.findByEmailUsuario(email);
		if (op.isEmpty()) {
			throw new ResponseStatusException(HttpStatus.NOT_FOUND,"as");
		}
		return op.get();
	}
	
	
}

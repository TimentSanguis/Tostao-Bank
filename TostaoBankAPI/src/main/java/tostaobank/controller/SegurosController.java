package tostaobank.controller;

import java.time.ZonedDateTime;
import java.util.List;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.server.ResponseStatusException;

import tostaobank.Utils.SeguroUtils;
import tostaobank.model.Historico;
import tostaobank.model.Seguros;
import tostaobank.model.Usuario;
import tostaobank.repository.HistoricoRepository;
import tostaobank.repository.SegurosRepository;
import tostaobank.repository.UsuarioRepository;
import tostaobank.service.CachingService;

@RestController
@RequestMapping(value = "/seguros")
public class SegurosController {
	
	@Autowired
	private CachingService cacheS;
	@Autowired
	private UsuarioRepository repU;	
	@Autowired
	private HistoricoRepository repH;
	@Autowired
	private SegurosRepository repS;
	
	@GetMapping(value = "/segs")
	public List<Seguros> listarTodos(@RequestParam String email) {
		return repS.findByEmailUsuario(email);
	}
	
	@PostMapping(value = "/contratar")
	public Seguros contratar (@RequestBody Seguros novoSeg) {
		Optional<Usuario> oo = repU.findByEmailUsuario(novoSeg.getEmailUsuario());
		if (oo.isEmpty()) {
			throw new ResponseStatusException(HttpStatus.NOT_FOUND,"Usuario inválido");
		}
		
		Usuario user = oo.get();
		double cobertura = novoSeg.getValorCobertura();
		
		if (cobertura <= 0) {
			throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Valor inválido!");
		}
	    if (!SeguroUtils.tipoValido(novoSeg.getTipoSeguro())) {
	        throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Tipo de seguro inválido");
	    }
	    double coberturaDiv = SeguroUtils.obterFator(novoSeg.getTipoSeguro()); 
	    double preco = cobertura / coberturaDiv;
	    novoSeg.setValorSeguro(preco);
	    
	    if (user.getSaldoUsuario() < preco) {
	    	throw new ResponseStatusException(HttpStatus.BAD_REQUEST,"Saldo insuficiente");
	    }
	    
		Optional<Seguros> op = repS.findByEmailUsuarioAndTipoSeguro(novoSeg.getEmailUsuario(), novoSeg.getTipoSeguro());
		// aqui só vai mexer se já tiver algum seguro do tipo
		if (op.isPresent()) {
			Seguros seg = op.get();
			if(seg.isStatusSeguro() == true) {
				throw new ResponseStatusException(HttpStatus.CONFLICT,"seguro já contratado");
			} else {
			seg.setStatusSeguro(true);
			repS.save(seg);
			return seg;
		}}
		// salva no historico, seguros e saldo do usuario
		Historico transacao = new Historico();
		transacao.setEmailTransferencia(user.getEmailUsuario());
		transacao.setValorTransferencia(novoSeg.getValorSeguro());
		transacao.setTipoTransferencia("Contratação de Seguro");
		transacao.setDataTransferencia(ZonedDateTime.now());
		transacao.setDescTransferencia(novoSeg.getTipoSeguro());
		repH.save(transacao);
		cacheS.apagarCache();
		
		novoSeg.setDataInicio(ZonedDateTime.now());
		novoSeg.setDataTermino(ZonedDateTime.now().plusYears(1));
		novoSeg.setStatusSeguro(true);
		repS.save(novoSeg);
		
		user.setSaldoUsuario(user.getSaldoUsuario() - novoSeg.getValorSeguro());
		repU.save(user);
		cacheS.apagarCache();
		return novoSeg;
	}
	
	@DeleteMapping(value = "/cancelar")
	public Seguros cancelar(@RequestBody Seguros cancelSeg) {
		Optional<Seguros> op = repS.findByEmailUsuarioAndTipoSeguro(cancelSeg.getEmailUsuario(), cancelSeg.getTipoSeguro());
		if (op.isEmpty()) {
			throw new ResponseStatusException(HttpStatus.BAD_REQUEST,"Nenhum seguro");
		}
		Seguros seg = op.get();
		repS.delete(seg);
		cacheS.apagarCache();
		return seg;
	}
	
	@PostMapping(value = "/mudastatus")
	public Seguros mudarStatus(@RequestBody Seguros seguro) {
		Optional<Seguros> op = repS.findByEmailUsuarioAndTipoSeguro(seguro.getEmailUsuario(), seguro.getTipoSeguro());
		if (op.isEmpty()) {
			throw new ResponseStatusException(HttpStatus.BAD_REQUEST,"Nenhum seguro");
		}
		
		Seguros seg = op.get();
		seg.setStatusSeguro(!seg.isStatusSeguro());
		cacheS.apagarCache();
		return repS.save(seg);
	}
}

package tostaobank.controller;

import java.util.List;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.server.ResponseStatusException;

import tostaobank.model.Cartoes;
import tostaobank.model.Usuario;
import tostaobank.repository.CartoesRepository;
import tostaobank.repository.UsuarioRepository;
import tostaobank.service.CachingService;

@RestController
@RequestMapping(value = "/cartoes")
public class CartoesController {

	@Autowired
	private CartoesRepository repC;
	@Autowired 
	private CachingService cacheS;
	@Autowired
	private UsuarioRepository repU;
	
	@GetMapping(value = "/acha/{email}")
	List<Cartoes> acha(@PathVariable String email){
		return cacheS.cartoesUser(email);
	}
	
	@PostMapping(value = "/novo")
	public Cartoes novoCartao(@RequestBody Cartoes novoCartao) {
		String email = novoCartao.getUsuario().getEmailUsuario();
		Optional<Usuario> aa = repU.findByEmailUsuario(email);
		if (aa.isPresent()) {
			Usuario user = aa.get();
			String cartao = UsuarioController.geraCartao();
			novoCartao.setNumCartoes(cartao);
			novoCartao.setUsuario(user);
			cacheS.apagarCache();
			return repC.save(novoCartao);
		}
		throw new ResponseStatusException(HttpStatus.NOT_FOUND,"usuario não encontrado" + email);
	}
	
	@DeleteMapping(value = "/cancelar")
	public Cartoes cancelarCartao(@RequestBody Cartoes cartao) {
		Optional<Cartoes> op = repC.findByNumCartoes(cartao.getNumCartoes());
		if (op.isPresent()) {
			Cartoes cartaoAntigo = op.get();
			repC.delete(cartaoAntigo);
			cacheS.apagarCache();
			return cartaoAntigo;
		}
		throw new ResponseStatusException(HttpStatus.NOT_FOUND,"Cartão não encontrado");
	}
}

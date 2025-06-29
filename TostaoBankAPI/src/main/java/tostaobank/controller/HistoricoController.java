package tostaobank.controller;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import tostaobank.model.Historico;
import tostaobank.repository.HistoricoRepository;
import tostaobank.service.CachingService;

@RestController
@RequestMapping(value = "/historico")
public class HistoricoController {
	
	@Autowired
	private CachingService cacheM;	
	@Autowired
	private HistoricoRepository repH;
	
	@GetMapping(value = "/transferencias")
	public List<Historico> retornaTransferencias(@RequestParam String email){
		return cacheM.findByemailTransferencia(email);
	}
	
	
}

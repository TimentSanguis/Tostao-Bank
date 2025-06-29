package tostaobank.service;
import java.util.List;
import java.util.Optional;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.cache.annotation.CacheEvict;
import org.springframework.cache.annotation.Cacheable;
import org.springframework.stereotype.Service;

import tostaobank.model.Cartoes;
import tostaobank.model.Historico;
import tostaobank.model.Investimento;
import tostaobank.model.Seguros;
import tostaobank.model.Usuario;
import tostaobank.repository.CartoesRepository;
import tostaobank.repository.HistoricoRepository;
import tostaobank.repository.InvestimentoRepository;
import tostaobank.repository.SegurosRepository;
import tostaobank.repository.UsuarioRepository;

@Service
public class CachingService {
	
	@Autowired
	private UsuarioRepository repU;	
	@Autowired 
	private HistoricoRepository repH;	
	@Autowired
	private InvestimentoRepository repI;
	@Autowired
	private CartoesRepository repC;
	@Autowired
	private SegurosRepository repS;
	
	@Cacheable(value = "cartoes", key = "#email")
	public List<Cartoes>cartoesUser(String email){
		return repC.findByUsuario_EmailUsuario(email);
	}
	
	@Cacheable(value = "usuario", key = "#email")
	public Optional<Usuario>buscarPorEmail(String email){
		return repU.findByEmailUsuario(email);
	}
	
	@Cacheable(value = "transferencias", key = "#email")
	public List<Historico> findByemailTransferencia(String email){
		return repH.findByEmailTransferenciaOrEmailRecebeTransferenciaOrderByDataTransferenciaDesc(email,email);
	}
	
	@Cacheable(value = "investimento", key = "#email")
	public List<Investimento> retornaInvestimentos(String email){
		return repI.findByEmailUsuario(email);
	}
	
	@Cacheable(value = "seguros", key = "#email")
	public List<Seguros> findByEmailUsuario(String email){
		return repS.findByEmailUsuario(email);
	}
	
	@CacheEvict(value = {"transferencias","investimento","cartoes","seguros"}, allEntries = true)
	public void apagarCache() {
		System.out.println("Removendo cache!");
	}
}

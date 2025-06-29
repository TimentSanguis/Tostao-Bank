package tostaobank.repository;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;

import tostaobank.model.Historico;

public interface HistoricoRepository extends JpaRepository<Historico,Long>{
	List<Historico> findByEmailTransferenciaOrEmailRecebeTransferenciaOrderByDataTransferenciaDesc(String email,String emailRecebe);
}

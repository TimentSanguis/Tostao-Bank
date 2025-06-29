package tostaobank.repository;

import java.util.List;
import java.util.Optional;

import org.springframework.data.jpa.repository.JpaRepository;

import tostaobank.model.Investimento;

public interface InvestimentoRepository extends JpaRepository<Investimento, Long>{
	Optional<Investimento> findByEmailUsuarioAndTipoInvestimento(String email,String tipoInvestimento);
	List<Investimento> findByEmailUsuario(String email);
}

package tostaobank.repository; 

import java.util.List;
import java.util.Optional;

import org.springframework.data.jpa.repository.JpaRepository;

import tostaobank.model.Seguros;

public interface SegurosRepository extends JpaRepository<Seguros, Long>{
	Optional<Seguros> findByEmailUsuarioAndTipoSeguro (String emailUsuario,String tipoSeguro);
	List<Seguros> findByEmailUsuario(String email);
}

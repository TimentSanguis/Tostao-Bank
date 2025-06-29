package tostaobank.repository;

import java.util.List;
import java.util.Optional;

import org.springframework.data.jpa.repository.JpaRepository;

import tostaobank.model.Cartoes;
import tostaobank.model.Usuario;

public interface CartoesRepository extends JpaRepository<Cartoes,Long>{
	Optional<Cartoes> findByUsuario(Usuario usuario);
	Optional<Cartoes> findByNumCartoes(String numCartoes);
	List<Cartoes> findByUsuario_EmailUsuario(String emailUsuario);
}

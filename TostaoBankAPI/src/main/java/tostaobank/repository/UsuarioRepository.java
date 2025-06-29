package tostaobank.repository;

import java.util.List;
import java.util.Optional;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import jakarta.transaction.Transactional;
import tostaobank.model.Usuario;

@Transactional
public interface UsuarioRepository extends JpaRepository<Usuario, Long>{
	List<Usuario> findAll();
	Optional<Usuario> findByEmailUsuario(String email);
	
	@Query("SELECT u.emailUsuario FROM Usuario u WHERE u.idUsuario = :id")
    String findEmailById(@Param("id") Long id);
}

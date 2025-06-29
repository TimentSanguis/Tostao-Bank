package tostaobank.service;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import tostaobank.model.Usuario;
import tostaobank.repository.UsuarioRepository;

@Service
public class UsuarioService {

    @Autowired
    private UsuarioRepository repU;

    public Usuario buscarPorId(Long id) {
        return repU.findById(id)
                .orElseThrow(() -> new RuntimeException("Usuário não encontrado"));
    }
}

package tostaobank.model;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;

@Entity
@Table(name = "Cartoes")
public class Cartoes {

	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	private Long idCartoes;
	private String numCartoes;
	@ManyToOne
	@JoinColumn (name = "idUsuario", referencedColumnName = "idUsuario")
	private Usuario usuario;
	
	
	public Cartoes() {
		super();
	}

	public Cartoes(String numCartoes, Usuario usuario) {
		super();
		this.numCartoes = numCartoes;
		this.usuario = usuario;
	}
	
	public Long getIdCartoes() {
		return idCartoes;
	}
	public void setIdCartoes(Long idCartoes) {
		this.idCartoes = idCartoes;
	}
	public String getNumCartoes() {
		return numCartoes;
	}
	public void setNumCartoes(String numCartoes) {
		this.numCartoes = numCartoes;
	}
	public Usuario getUsuario() {
		return usuario;
	}
	public void setUsuario(Usuario usuario) {
		this.usuario = usuario;
	}
	
	
}

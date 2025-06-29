package tostaobank.model;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

@Entity
@Table(name = "Usuario")
public class Usuario {

	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	private long idUsuario;	
	private String nomeUsuario;	
	private String emailUsuario;	
	private String senhaUsuario;	
	private Double saldoUsuario = 0.0;	
	private String cartaoUsuario;	
	private String telefoneUsuario;
	
	public Usuario() {
		super();
	}
		
	public Usuario(long idUsuario, String nomeUsuario, String emailUsuario, String senhaUsuario, Double saldoUsuario,
			String cartaoUsuario, String telefoneUsuario) {
		super();
		this.idUsuario = idUsuario;
		this.nomeUsuario = nomeUsuario;
		this.emailUsuario = emailUsuario;
		this.senhaUsuario = senhaUsuario;
		this.saldoUsuario = saldoUsuario;
		this.cartaoUsuario = cartaoUsuario;
		this.telefoneUsuario = telefoneUsuario;
	}

	
	public long getIdUsuario() {
		return idUsuario;
	}
	public void setIdUsuario(long idUsuario) {
		this.idUsuario = idUsuario;
	}
	public String getNomeUsuario() {
		return nomeUsuario;
	}
	public void setNomeUsuario(String nomeUsuario) {
		this.nomeUsuario = nomeUsuario;
	}
	public String getEmailUsuario() {
		return emailUsuario;
	}
	public void setEmailUsuario(String emailUsuario) {
		this.emailUsuario = emailUsuario;
	}
	public String getSenhaUsuario() {
		return senhaUsuario;
	}
	public void setSenhaUsuario(String senhaUsuario) {
		this.senhaUsuario = senhaUsuario;
	}
	public Double getSaldoUsuario() {
		return saldoUsuario;
	}
	public void setSaldoUsuario(Double saldoUsuario) {
		this.saldoUsuario = saldoUsuario;
	}
	public String getCartaoUsuario() {
		return cartaoUsuario;
	}
	public void setCartaoUsuario(String cartaoUsuario) {
		this.cartaoUsuario = cartaoUsuario;
	}
	public String getTelefoneUsuario() {
		return telefoneUsuario;
	}
	public void setTelefoneUsuario(String telefoneUsuario) {
		this.telefoneUsuario = telefoneUsuario;
	}

	
}

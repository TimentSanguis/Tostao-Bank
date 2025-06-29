package tostaobank.model;

import java.time.ZonedDateTime;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;


@Entity
@Table(name = "Seguros")
public class Seguros {
	
	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	private long idSeguro;
	private String emailUsuario;
	private String tipoSeguro;
	private double valorSeguro;
	private double valorCobertura;
	private ZonedDateTime dataInicio;
	private ZonedDateTime dataTermino;
	private boolean statusSeguro;
	
	public Seguros() {}
	
	public Seguros(String tipoSeguro, double valorSeguro, double valorCobertura, ZonedDateTime dataInicio,
			ZonedDateTime dataTermino, boolean statusSeguro, String emailUsuario) {
		super();
		this.tipoSeguro = tipoSeguro;
		this.valorSeguro = valorSeguro;
		this.valorCobertura = valorCobertura;
		this.dataInicio = dataInicio;
		this.dataTermino = dataTermino;
		this.statusSeguro = statusSeguro;
		this.emailUsuario = emailUsuario;
	}
	
	public long getIdSeguro() {
		return idSeguro;
	}
	public void setIdSeguro(long idSeguro) {
		this.idSeguro = idSeguro;
	}
	public String getTipoSeguro() {
		return tipoSeguro;
	}
	public void setTipoSeguro(String tipoSeguro) {
		this.tipoSeguro = tipoSeguro;
	}
	public double getValorSeguro() {
		return valorSeguro;
	}
	public void setValorSeguro(double valorSeguro) {
		this.valorSeguro = valorSeguro;
	}
	public double getValorCobertura() {
		return valorCobertura;
	}
	public void setValorCobertura(double valorCobertura) {
		this.valorCobertura = valorCobertura;
	}
	public ZonedDateTime getDataInicio() {
		return dataInicio;
	}
	public void setDataInicio(ZonedDateTime dataInicio) {
		this.dataInicio = dataInicio;
	}
	public ZonedDateTime getDataTermino() {
		return dataTermino;
	}
	public void setDataTermino(ZonedDateTime dataTermino) {
		this.dataTermino = dataTermino;
	}
	public boolean isStatusSeguro() {
		return statusSeguro;
	}
	public void setStatusSeguro(boolean statusSeguro) {
		this.statusSeguro = statusSeguro;
	}
	public String getEmailUsuario() {
		return emailUsuario;
	}
	public void setEmailUsuario(String emailUsuario) {
		this.emailUsuario = emailUsuario;
	}
	
	
}

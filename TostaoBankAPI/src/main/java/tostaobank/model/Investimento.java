package tostaobank.model;

import java.time.ZonedDateTime;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

@Entity
@Table(name = "Investimento")
public class Investimento {

	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	private long idInvestimento;
	private Double valorInvestimento;
	private String tipoInvestimento;
	private String emailUsuario;
	private ZonedDateTime dataInvestimento;
	
	
	public Investimento() {
		super();
	}
	
	public Investimento(long idInvestimento, Double valorInvestimento, String tipoInvestimento, String emailUsuario,
			ZonedDateTime dataInvestimento) {
		super();
		this.idInvestimento = idInvestimento;
		this.valorInvestimento = valorInvestimento;
		this.tipoInvestimento = tipoInvestimento;
		this.emailUsuario = emailUsuario;
		this.dataInvestimento = dataInvestimento;
	}
	
	public long getIdInvestimento() {
		return idInvestimento;
	}
	public void setIdInvestimento(long idInvestimento) {
		this.idInvestimento = idInvestimento;
	}
	public Double getValorInvestimento() {
		return valorInvestimento;
	}
	public void setValorInvestimento(Double valorInvestimento) {
		this.valorInvestimento = valorInvestimento;
	}
	public String getTipoInvestimento() {
		return tipoInvestimento;
	}
	public void setTipoInvestimento(String tipoInvestimento) {
		this.tipoInvestimento = tipoInvestimento;
	}
	public String getEmailUsuario() {
		return emailUsuario;
	}
	public void setEmailUsuario(String emailUsuario) {
		this.emailUsuario = emailUsuario;
	}
	public ZonedDateTime getDataInvestimento() {
		return dataInvestimento;
	}
	public void setDataInvestimento(ZonedDateTime dataInvestimento) {
		this.dataInvestimento = dataInvestimento;
	}
	
}

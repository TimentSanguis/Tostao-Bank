package tostaobank.model;

import java.time.ZonedDateTime;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

@Entity
@Table(name = "Historico")
public class Historico {
	
	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	private long idTransferencia;
	private String emailTransferencia;
	private Double valorTransferencia;
	private String tipoTransferencia;
	private String emailRecebeTransferencia;
	private ZonedDateTime dataTransferencia;
	private String descTransferencia;
	private String telefoneTransferencia;
	
	public Historico() {}

	public Historico(String emailTransferencia, Double valorTransferencia,
			String tipoTransferencia, String emailRecebeTransferencia, ZonedDateTime dataTransferencia,
			String descTransferencia, String telefoneTransferencia) {
		super();
		this.emailTransferencia = emailTransferencia;
		this.valorTransferencia = valorTransferencia;
		this.tipoTransferencia = tipoTransferencia;
		this.emailRecebeTransferencia = emailRecebeTransferencia;
		this.dataTransferencia = dataTransferencia;
		this.descTransferencia = descTransferencia;
		this.telefoneTransferencia = telefoneTransferencia;
	}
	
	public Historico(String emailTransferencia, Double valorTransferencia,
			String tipoTransferencia, ZonedDateTime dataTransferencia, String descTransferencia,
			String telefoneTransferencia) {
		super();
		this.emailTransferencia = emailTransferencia;
		this.valorTransferencia = valorTransferencia;
		this.tipoTransferencia = tipoTransferencia;
		this.dataTransferencia = dataTransferencia;
		this.descTransferencia = descTransferencia;
		this.telefoneTransferencia = telefoneTransferencia;
	}

	public Historico(String emailTransferencia, Double valorTransferencia,
			String tipoTransferencia, String emailRecebeTransferencia, ZonedDateTime dataTransferencia,
			String descTransferencia) {
		super();
		this.emailTransferencia = emailTransferencia;
		this.valorTransferencia = valorTransferencia;
		this.tipoTransferencia = tipoTransferencia;
		this.emailRecebeTransferencia = emailRecebeTransferencia;
		this.dataTransferencia = dataTransferencia;
		this.descTransferencia = descTransferencia;
	}
	
	public long getIdTransferencia() {
		return idTransferencia;
	}

	public void setIdTransferencia(long idTransferencia) {
		this.idTransferencia = idTransferencia;
	}

	public String getEmailTransferencia() {
		return emailTransferencia;
	}

	public void setEmailTransferencia(String emailTransferencia) {
		this.emailTransferencia = emailTransferencia;
	}

	public Double getValorTransferencia() {
		return valorTransferencia;
	}

	public void setValorTransferencia(Double valorTransferencia) {
		this.valorTransferencia = valorTransferencia;
	}

	public String getTipoTransferencia() {
		return tipoTransferencia;
	}

	public void setTipoTransferencia(String tipoTransferencia) {
		this.tipoTransferencia = tipoTransferencia;
	}

	public String getEmailRecebeTransferencia() {
		return emailRecebeTransferencia;
	}

	public void setEmailRecebeTransferencia(String emailRecebeTransferencia) {
		this.emailRecebeTransferencia = emailRecebeTransferencia;
	}

	public ZonedDateTime getDataTransferencia() {
		return dataTransferencia;
	}

	public void setDataTransferencia(ZonedDateTime dataTransferencia) {
		this.dataTransferencia = dataTransferencia;
	}

	public String getDescTransferencia() {
		return descTransferencia;
	}

	public void setDescTransferencia(String descTransferencia) {
		this.descTransferencia = descTransferencia;
	}

	public String getTelefoneTransferencia() {
		return telefoneTransferencia;
	}

	public void setTelefoneTransferencia(String telefoneTransferencia) {
		this.telefoneTransferencia = telefoneTransferencia;
	};
	

}

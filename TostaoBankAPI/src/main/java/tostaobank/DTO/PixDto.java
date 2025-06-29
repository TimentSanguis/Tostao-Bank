package tostaobank.DTO;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Positive;

public class PixDto {
	@NotBlank(message = "Email do remetente é obrigatório")
	private String email;
	@NotNull(message = "Valor é obrigatório")
    @Positive(message = "O valor deve ser positivo")
	private double valor;
	@NotBlank(message = "Email do remetente é obrigatório")
	private String emailRecebe;
	
	
	public PixDto() {
		super();
	}
	
	public PixDto(String email, double valor, String emailRecebe) {
		super();
		this.email = email;
		this.valor = valor;
		this.emailRecebe = emailRecebe;
	}
	
	public String getEmail() {
		return email;
	}
	public void setEmail(String email) {
		this.email = email;
	}
	public double getValor() {
		return valor;
	}
	public void setValor(double valor) {
		this.valor = valor;
	}
	public String getEmailRecebe() {
		return emailRecebe;
	}
	public void setEmailRecebe(String emailRecebe) {
		this.emailRecebe = emailRecebe;
	}
	
	
}

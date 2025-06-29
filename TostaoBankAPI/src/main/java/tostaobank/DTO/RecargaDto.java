package tostaobank.DTO;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Positive;

public class RecargaDto {
	@NotBlank(message = "Email do remetente é obrigatório")
	private String email;
	@NotNull(message = "Valor é obrigatório")
    @Positive(message = "O valor deve ser positivo")
	private double valor;
	@NotBlank(message = "número de telefone é obrigatório")
	private String telefone;
	
	public RecargaDto() {
		super();
	}
	
	public RecargaDto(@NotBlank(message = "Email do remetente é obrigatório") String email,
			@NotNull(message = "Valor é obrigatório") @Positive(message = "O valor deve ser positivo") double valor,
			@NotBlank(message = "número de telefone é obrigatório") String telefone) {
		super();
		this.email = email;
		this.valor = valor;
		this.telefone = telefone;
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
	public String getTelefone() {
		return telefone;
	}
	public void setTelefone(String telefone) {
		this.telefone = telefone;
	}
	
}

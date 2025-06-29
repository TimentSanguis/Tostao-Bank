package tostaobank.Utils;

import java.util.Map;

public class SeguroUtils {
	public static Map<String, Double> fator = Map.of(
            "Seguro Residencial", 343.0,
            "Seguro de Vida", 280.0,
            "Seguro de Automóvel", 400.0
    );

    public static double obterFator(String tipo) {
        return fator.getOrDefault(tipo, 0.0);
    }

    public static boolean tipoValido(String tipo) {
        return fator.containsKey(tipo);
    }
}
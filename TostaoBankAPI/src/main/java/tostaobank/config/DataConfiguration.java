package tostaobank.config;

import javax.sql.DataSource;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.jdbc.datasource.DriverManagerDataSource;
import org.springframework.orm.jpa.JpaVendorAdapter;
import org.springframework.orm.jpa.vendor.Database;
import org.springframework.orm.jpa.vendor.HibernateJpaVendorAdapter;
import org.springframework.web.servlet.config.annotation.CorsRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;


@Configuration
public class DataConfiguration {
	
	@Bean
    public WebMvcConfigurer corsConfigurer() {
        return new WebMvcConfigurer() {
            @Override
            public void addCorsMappings(CorsRegistry registry) {
                registry.addMapping("/**").allowedOrigins("*").allowedMethods("*");
            }
        };
    }
	
	@Bean
	public DataSource dataSource() {
	    DriverManagerDataSource dataSource = new DriverManagerDataSource();
	    dataSource.setDriverClassName("org.h2.Driver");
	    dataSource.setUrl("jdbc:h2:mem:tostaobank;DB_CLOSE_DELAY=-1;DB_CLOSE_ON_EXIT=FALSE");
	    dataSource.setUsername("sa");
	    dataSource.setPassword("");
	    return dataSource;
	}

	@Bean
	public JpaVendorAdapter jpaVendorAdapter() {
	    HibernateJpaVendorAdapter adapter = new HibernateJpaVendorAdapter();
	    adapter.setDatabase(Database.H2);
	    adapter.setShowSql(true);
	    adapter.setGenerateDdl(true);
	    adapter.setDatabasePlatform("org.hibernate.dialect.H2Dialect");
	    adapter.setPrepareConnection(true);
	    return adapter; 
	}
}
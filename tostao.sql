-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/04/2025 às 04:08
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tostao`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico`
--

CREATE TABLE `historico` (
  `id_Transferencia` int(11) NOT NULL,
  `email_recebe_Transferencia` varchar(50) NOT NULL,
  `data_Transferencia` date NOT NULL,
  `valor_Transferencia` decimal(10,0) NOT NULL,
  `email_Transferencia` varchar(50) NOT NULL,
  `tipo_Transferencia` varchar(22) NOT NULL,
  `desc_transferencia` varchar(255) NOT NULL,
  `telefone_transferencia` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `historico`
--

INSERT INTO `historico` (`id_Transferencia`, `email_recebe_Transferencia`, `data_Transferencia`, `valor_Transferencia`, `email_Transferencia`, `tipo_Transferencia`, `desc_transferencia`, `telefone_transferencia`) VALUES
(121, '', '2024-11-18', 109, 'jorge@gmail.com', 'Recarga', '', '977144953'),
(122, '', '2024-11-18', 1, 'jorge@gmail.com', 'Recarga', '', '977144953'),
(123, '', '2024-11-18', 2, 'jorge@gmail.com', 'Recarga', '', '977144953'),
(124, 'rato@gmail.com', '2024-11-18', 2, 'jorge@gmail.com', 'Pix', '', ''),
(125, 'rato@gmail.com', '2024-11-20', 100, 'hide@gmail.com', 'Pix', '', ''),
(126, 'rato@gmail.com', '2024-11-20', 100, 'hide@gmail.com', 'Pix', '', ''),
(127, 'giovanni2@gmail.com', '2024-11-20', 100, 'hide@gmail.com', 'Pix', '', ''),
(128, '', '2024-11-20', 500, 'hide@gmail.com', 'Reserva de Emergência', 'Investido', ''),
(129, '', '2024-11-20', 200, 'hide@gmail.com', 'Reserva de Emergência', 'Retirada', ''),
(132, '', '2024-11-24', 2000, 'hide@gmail.com', 'Reserva de Emergência', 'Investido', ''),
(135, '', '2024-11-24', 2000, 'hide@gmail.com', 'Recarga', '', '32543543'),
(136, '', '2024-11-24', 2000, 'hide@gmail.com', 'Recarga', '', '977144953'),
(138, '', '2024-11-24', 2, 'hide@gmail.com', 'Recarga', '', '977144953'),
(139, '', '2024-11-24', 2, 'hide@gmail.com', 'Recarga', '', '977144953'),
(141, '', '2024-11-24', 100, 'hide@gmail.com', 'Recarga', '', '977144953'),
(142, '', '2024-11-24', 20, 'hide@gmail.com', 'Recarga', '', '977144953'),
(143, '', '2024-11-24', 20, 'hide@gmail.com', 'Recarga', '', '977144953'),
(144, '', '2024-11-24', 200, 'hide@gmail.com', 'Recarga', '', '977144953'),
(145, '', '2024-11-24', 20, 'hide@gmail.com', 'Recarga', '', '977144953'),
(146, '', '2024-11-24', 20, 'hide@gmail.com', 'Recarga', '', '977144953'),
(147, '', '2024-11-24', 20, 'hide@gmail.com', 'Recarga', '', '977144953'),
(148, '', '2024-11-24', 20, 'hide@gmail.com', 'Recarga', '', '977144953'),
(149, 'alan@gmail.com', '2024-11-24', 200, 'hide@gmail.com', 'Pix', '', ''),
(150, 'alan@gmail.com', '2024-11-24', 100, 'hide@gmail.com', 'Pix', '', ''),
(151, 'alan@gmail.com', '2024-11-24', 2, 'hide@gmail.com', 'Pix', '', ''),
(152, 'rato@gmail.com', '2024-11-24', 100, 'hide@gmail.com', 'Pix', '', ''),
(153, 'rato@gmail.com', '2024-11-24', 3, 'hide@gmail.com', 'Pix', '', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `investimento`
--

CREATE TABLE `investimento` (
  `id_investimento` int(11) NOT NULL,
  `valor_investimento` decimal(10,0) NOT NULL,
  `tipo_investimento` text NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `data_investimento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `investimento`
--

INSERT INTO `investimento` (`id_investimento`, `valor_investimento`, `tipo_investimento`, `cliente_id`, `data_investimento`) VALUES
(2, 50, 'Meu sonho de consumo', 3, '2024-11-01 15:44:00'),
(3, 100, 'Reserva de Emergência', 3, '2024-11-01 15:44:18'),
(4, 60, 'Fazer uma viagem', 3, '2024-11-15 19:42:05'),
(7, 80, 'Fazer uma viagem', 15, '2024-11-18 15:01:20'),
(8, 80, 'Meu sonho de consumo', 15, '2024-11-18 15:02:27'),
(9, 20, 'Reserva de Emergência', 15, '2024-11-18 17:10:01'),
(10, 2300, 'Reserva de Emergência', 4, '2024-11-20 15:08:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nome_usuario` varchar(60) NOT NULL,
  `email_usuario` varchar(80) NOT NULL,
  `senha_usuario` varchar(60) NOT NULL,
  `saldo_usuario` decimal(60,0) NOT NULL,
  `num_cartao` bigint(20) UNSIGNED DEFAULT NULL,
  `telefone_usuario` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome_usuario`, `email_usuario`, `senha_usuario`, `saldo_usuario`, `num_cartao`, `telefone_usuario`) VALUES
(2, 'Alan', 'alan@gmail.com', '4321', 58379, 9172908520487052, '11930507782'),
(3, 'Thiago', 'thigas@gmail.com', '12', 1015, 1088785334572927, '21474836473'),
(4, 'Hideaki Gameplays', 'hide@gmail.com', 'hide', 199932559, 3925201388006286, '21942405567'),
(13, 'Rato maluco', 'rato@gmail.com', '123', 5526, 6478883265963646, '11912445066'),
(14, 'Giovanni', 'giovanni2@gmail.com', '1234', 724, 2082716325374987, '11977144953'),
(15, 'Jorge', 'jorge@gmail.com', '1234', 72726, 7508393214557685, '11757545235'),
(16, 'Convidado', '', '', 1000000, 7216464644499555, '00999999999');

--
-- Acionadores `usuario`
--
DELIMITER $$
CREATE TRIGGER `gerar_num_cartao` BEFORE INSERT ON `usuario` FOR EACH ROW BEGIN
    SET NEW.num_cartao = FLOOR(1000000000000000 + RAND() * 8999999999999999);
END
$$
DELIMITER ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id_Transferencia`);

--
-- Índices de tabela `investimento`
--
ALTER TABLE `investimento`
  ADD PRIMARY KEY (`id_investimento`),
  ADD KEY `fk_cliente_usuario` (`cliente_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `num_cartao` (`num_cartao`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `historico`
--
ALTER TABLE `historico`
  MODIFY `id_Transferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT de tabela `investimento`
--
ALTER TABLE `investimento`
  MODIFY `id_investimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `investimento`
--
ALTER TABLE `investimento`
  ADD CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`cliente_id`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

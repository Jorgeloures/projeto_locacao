-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/05/2026 às 20:59
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
-- Banco de dados: `locadora`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `nome` varchar(150) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `telefone1` varchar(20) DEFAULT NULL,
  `telefone2` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `data_cadastro`, `nome`, `cpf`, `endereco`, `cidade`, `estado`, `cep`, `telefone1`, `telefone2`, `email`) VALUES
(1, '2026-04-26 10:01:18', 'José da Silva', '123.456.789-00', 'Av. Centro, 110', 'Cataguases', 'MG', '12345-678', '(32) 98888-8888', '(32) 98899-9988', 'jose@hotmail.com'),
(2, '2026-04-26 10:02:41', 'Mariano Silva', '987.654.321-10', 'Av. Sistema, 85', 'Juiz de Fora', 'MG', '65478-321', '(32) 98666-8888', '(32) 97856-1234', 'Mariano@gmail.com'),
(6, '2026-05-08 13:02:53', 'Amélia das Cabras', '987.654.321-10', 'Av. Sistema, 85', 'Rio de Janeiro', 'RJ', '65478-321', '(21) 98877-7788', '(21) 98877-7788', 'cabras@gmail.com'),
(7, '2026-05-08 13:04:21', 'Gregório dos Santos', '123.456.789-00', 'Av. Brasileira, n° 123', 'Leopoldina', 'MG', '65498-710', '(32) 98888-8888', '(32) 98855-5588', 'greg@hotmail.com'),
(8, '2026-05-08 13:05:38', 'Maria Taveira', '456.789.310-00', 'Rua das Marias', 'Cataguases', 'MG', '65498-710', '(32) 98866-4444', '(32) 98866-44466', 'mariataveira@hotmail.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipamentos`
--

CREATE TABLE `equipamentos` (
  `id_equipamento` int(11) NOT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `descricao` varchar(150) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `quantidade_total` int(11) NOT NULL DEFAULT 0,
  `valor_locacao_dia` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `equipamentos`
--

INSERT INTO `equipamentos` (`id_equipamento`, `data_cadastro`, `descricao`, `categoria`, `quantidade_total`, `valor_locacao_dia`) VALUES
(1, '2026-04-26 10:06:32', 'Computador Lenovo CL 1000', 'Computadores', 10, 21.00),
(2, '2026-04-26 10:06:59', 'Computador HP 300', 'Computadores', 6, 22.00),
(3, '2026-04-26 10:07:33', 'Notebook Dell', 'Notebooks', 8, 32.00),
(4, '2026-04-26 10:08:11', 'Notebook HP 6500', 'Notebooks', 5, 31.00),
(5, '2026-04-26 10:08:49', 'Impressora HP 600', 'Impressoras', 5, 32.00),
(6, '2026-04-26 10:09:32', 'Impressora Xerox Toner c/ toner', 'Impressoras', 5, 125.00),
(8, '2026-04-12 13:07:39', 'Estabilizador Positivo S10', 'Utilitários', 14, 7.50),
(9, '2026-05-12 13:08:18', 'Notebook Dell Inspirion I7', 'Notebooks', 0, 33.00),
(10, '2026-05-12 13:08:59', 'computador Apple M5000', 'Computadores', 7, 25.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_locacao`
--

CREATE TABLE `itens_locacao` (
  `id_item` int(11) NOT NULL,
  `id_locacao` int(11) NOT NULL,
  `id_equipamento` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `valor_total` decimal(10,2) DEFAULT 0.00,
  `entrega` enum('EFETIVA','MULTA','ANTECIPADA') DEFAULT 'EFETIVA',
  `multa` decimal(10,2) DEFAULT 0.00,
  `antecipacao` decimal(10,2) DEFAULT 0.00,
  `ajuste_desconto` decimal(10,2) DEFAULT 0.00,
  `ajuste_encargos` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_locacao`
--

INSERT INTO `itens_locacao` (`id_item`, `id_locacao`, `id_equipamento`, `quantidade`, `valor_unitario`, `valor_total`, `entrega`, `multa`, `antecipacao`, `ajuste_desconto`, `ajuste_encargos`) VALUES
(34, 21, 9, 2, 33.00, 198.00, 'MULTA', 10.53, 0.00, 0.53, 0.00),
(35, 21, 8, 2, 7.50, 45.00, 'MULTA', 10.53, 0.00, 0.53, 0.00),
(36, 22, 2, 5, 22.00, 660.00, 'MULTA', 222.20, 0.00, 0.20, 52.00),
(37, 23, 3, 2, 32.00, 128.00, 'EFETIVA', 0.00, 0.00, 0.00, 0.00),
(38, 24, 9, 4, 33.00, 396.00, 'ANTECIPADA', 0.00, 121.44, 0.56, 0.00),
(39, 25, 8, 4, 7.50, 240.00, 'EFETIVA', 0.00, 0.00, 0.00, 0.00),
(40, 25, 2, 4, 22.00, 704.00, 'EFETIVA', 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `locacao`
--

CREATE TABLE `locacao` (
  `id_locacao` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `data_locacao` datetime NOT NULL,
  `data_devolucao` datetime NOT NULL,
  `qtd_dias` int(11) NOT NULL,
  `status` enum('ABERTO','FECHADO') DEFAULT 'ABERTO',
  `data_dev_real` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `locacao`
--

INSERT INTO `locacao` (`id_locacao`, `id_cliente`, `data_locacao`, `data_devolucao`, `qtd_dias`, `status`, `data_dev_real`) VALUES
(21, 6, '2026-05-12 14:00:00', '2026-05-15 14:00:00', 3, 'FECHADO', '2026-05-15 17:00:00'),
(22, 1, '2026-05-12 14:00:00', '2026-05-18 14:00:00', 6, 'FECHADO', '2026-05-20 14:30:00'),
(23, 2, '2026-05-12 15:00:00', '2026-05-14 15:00:00', 2, 'ABERTO', NULL),
(24, 7, '2026-05-12 15:00:00', '2026-05-15 15:00:00', 3, 'FECHADO', '2026-05-14 17:00:00'),
(25, 8, '2026-05-12 15:00:00', '2026-05-20 15:00:00', 8, 'ABERTO', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('ADM','OPERADOR') NOT NULL DEFAULT 'OPERADOR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `login`, `senha`, `nivel`) VALUES
(1, 'Administrador', 'admin', '123', 'ADM'),
(2, 'jorge', 'jorge', '345', 'OPERADOR');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Índices de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  ADD PRIMARY KEY (`id_equipamento`);

--
-- Índices de tabela `itens_locacao`
--
ALTER TABLE `itens_locacao`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_locacao` (`id_locacao`),
  ADD KEY `id_equipamento` (`id_equipamento`);

--
-- Índices de tabela `locacao`
--
ALTER TABLE `locacao`
  ADD PRIMARY KEY (`id_locacao`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `equipamentos`
--
ALTER TABLE `equipamentos`
  MODIFY `id_equipamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `itens_locacao`
--
ALTER TABLE `itens_locacao`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de tabela `locacao`
--
ALTER TABLE `locacao`
  MODIFY `id_locacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itens_locacao`
--
ALTER TABLE `itens_locacao`
  ADD CONSTRAINT `itens_locacao_ibfk_1` FOREIGN KEY (`id_locacao`) REFERENCES `locacao` (`id_locacao`),
  ADD CONSTRAINT `itens_locacao_ibfk_2` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id_equipamento`);

--
-- Restrições para tabelas `locacao`
--
ALTER TABLE `locacao`
  ADD CONSTRAINT `locacao_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

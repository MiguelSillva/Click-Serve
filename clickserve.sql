-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/10/2025 às 23:11
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
-- Banco de dados: `clickserve`
create database clickserve;
use clickserve
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `ID_CategoriaProd` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`ID_CategoriaProd`, `nome`) VALUES
(1, 'Bebidas'),
(2, 'Carnes'),
(3, 'Grãos'),
(4, 'Outros');

-- --------------------------------------------------------

--
-- Estrutura para tabela `garcom`
--

CREATE TABLE `garcom` (
  `ID_garcom` int(11) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `Mesa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mesa`
--

CREATE TABLE `mesa` (
  `id_mesa` int(11) NOT NULL,
  `garcom_mesa` varchar(45) DEFAULT NULL,
  `n_pedido` int(11) DEFAULT NULL,
  `nome_cliente` varchar(45) DEFAULT NULL,
  `status_mesa` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mesa`
--

INSERT INTO `mesa` (`id_mesa`, `garcom_mesa`, `n_pedido`, `nome_cliente`, `status_mesa`) VALUES
(5, NULL, NULL, NULL, 1),
(6, 'root', NULL, '', 0),
(7, NULL, NULL, NULL, 1),
(8, NULL, NULL, NULL, 1),
(9, NULL, NULL, NULL, 1),
(10, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `ID_pedido` int(11) NOT NULL,
  `ID_Usuario` int(11) NOT NULL,
  `Mesa` int(11) NOT NULL,
  `Qtditens` int(11) NOT NULL,
  `Pago` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_garcom`
--

CREATE TABLE `pedido_garcom` (
  `ID_pedido` int(11) NOT NULL,
  `ID_garcom` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_produtos`
--

CREATE TABLE `pedido_produtos` (
  `id` int(11) NOT NULL,
  `id_mesa` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_produtos`
--

INSERT INTO `pedido_produtos` (`id`, `id_mesa`, `id_produto`, `quantidade`) VALUES
(21, 6, 2, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nomeProduto` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `quantidade_em_estoque` int(11) DEFAULT 0,
  `categoria_id` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nomeProduto`, `preco`, `quantidade_em_estoque`, `categoria_id`, `data_criacao`, `data_atualizacao`) VALUES
(1, 'TesteOutros', 4.00, 9, 4, '2025-05-05 01:34:24', '2025-10-15 03:14:20'),
(2, 'TesteBebidas', 10.00, 39, 1, '2025-05-05 01:35:20', '2025-10-15 21:09:51'),
(16, 'Laranja', 4.00, 439, 1, '2025-09-16 22:56:42', '2025-10-15 04:16:17'),
(18, 'cocateste', 5.00, 143, 1, '2025-10-15 03:14:09', '2025-10-15 04:17:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `telefone` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `telefone`, `senha`) VALUES
(1, 'Miguel', '', ''),
(2, 'root', '', 'senha'),
(3, 'Miguel', '', 'Miguel@20');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `data` datetime DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id`, `data`, `valor`, `usuario_id`) VALUES
(1, '2025-05-03 22:31:34', 150.00, 2),
(2, '2025-05-03 22:36:07', 120.00, 3),
(3, '2025-05-04 00:00:00', 100.00, 3),
(4, '2025-05-04 00:00:00', 170.00, 2),
(5, '2025-05-04 15:05:33', 120.00, 2),
(6, '2025-05-04 15:05:41', 190.00, 2),
(7, '2025-05-04 18:05:00', 620.00, 2),
(11, '2025-10-15 00:28:17', 5.00, 2),
(12, '2025-10-15 00:28:49', 17.00, 2),
(13, '2025-10-15 00:29:29', 10.00, 2),
(14, '2025-10-15 00:30:01', 5.00, 2),
(15, '2025-10-15 01:04:05', 10.00, 2),
(16, '2025-10-15 01:14:27', 54.00, 2),
(17, '2025-10-15 01:16:19', 10.00, 2),
(18, '2025-10-15 01:17:05', 5.00, 2),
(19, '2025-10-15 01:17:42', 15.00, 2),
(20, '2025-10-15 18:09:21', 10.00, 2);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`ID_CategoriaProd`);

--
-- Índices de tabela `garcom`
--
ALTER TABLE `garcom`
  ADD PRIMARY KEY (`ID_garcom`);

--
-- Índices de tabela `mesa`
--
ALTER TABLE `mesa`
  ADD PRIMARY KEY (`id_mesa`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`ID_pedido`),
  ADD KEY `ID_Usuario` (`ID_Usuario`);

--
-- Índices de tabela `pedido_garcom`
--
ALTER TABLE `pedido_garcom`
  ADD PRIMARY KEY (`ID_pedido`,`ID_garcom`),
  ADD KEY `ID_garcom` (`ID_garcom`);

--
-- Índices de tabela `pedido_produtos`
--
ALTER TABLE `pedido_produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mesa` (`id_mesa`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `mesa`
--
ALTER TABLE `mesa`
  MODIFY `id_mesa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `pedido_produtos`
--
ALTER TABLE `pedido_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `pedido_garcom`
--
ALTER TABLE `pedido_garcom`
  ADD CONSTRAINT `pedido_garcom_ibfk_1` FOREIGN KEY (`ID_pedido`) REFERENCES `pedido` (`ID_pedido`),
  ADD CONSTRAINT `pedido_garcom_ibfk_2` FOREIGN KEY (`ID_garcom`) REFERENCES `garcom` (`ID_garcom`);

--
-- Restrições para tabelas `pedido_produtos`
--
ALTER TABLE `pedido_produtos`
  ADD CONSTRAINT `fk_pedido_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `mesa` (`id_mesa`),
  ADD CONSTRAINT `fk_pedido_produto` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`ID_CategoriaProd`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

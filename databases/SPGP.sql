-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 17/06/2025 às 08:17
-- Versão do servidor: 11.8.1-MariaDB-4
-- Versão do PHP: 8.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `SPGP`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `menu_categorias`
--

CREATE TABLE `menu_categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `ordem` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `menu_categorias`
--

INSERT INTO `menu_categorias` (`id`, `nome`, `ordem`, `created_at`, `updated_at`) VALUES
(3, 'Configurações', 0, '2025-06-13 16:04:40', '2025-06-13 16:04:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `menu_itens`
--

CREATE TABLE `menu_itens` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `pagina` varchar(255) NOT NULL,
  `ordem` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `menu_itens`
--

INSERT INTO `menu_itens` (`id`, `categoria_id`, `nome`, `pagina`, `ordem`, `created_at`, `updated_at`) VALUES
(2, 3, 'Cadastro de Menus', '/spgp/admin/menus.php', 0, '2025-06-13 16:05:01', '2025-06-13 16:13:07'),
(3, 3, 'Configuração de Acesso', '/spgp/admin/setup.php', 0, '2025-06-13 16:05:18', '2025-06-13 16:13:07');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `menu_categorias`
--
ALTER TABLE `menu_categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `menu_itens`
--
ALTER TABLE `menu_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `menu_categorias`
--
ALTER TABLE `menu_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `menu_itens`
--
ALTER TABLE `menu_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `menu_itens`
--
ALTER TABLE `menu_itens`
  ADD CONSTRAINT `menu_itens_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `menu_categorias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

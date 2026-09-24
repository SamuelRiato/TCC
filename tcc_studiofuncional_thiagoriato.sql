-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/09/2026 às 13:24
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
-- Banco de dados: `tcc_studiofuncional_thiagoriato`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendaaulas`
--

CREATE TABLE `agendaaulas` (
  `idaula` int(11) NOT NULL,
  `idalunoaula` int(11) NOT NULL,
  `descricaoaula` text NOT NULL,
  `datahorainicio` datetime NOT NULL,
  `datahorafim` datetime NOT NULL,
  `vagastotais` int(11) NOT NULL CHECK (`vagastotais` > 0),
  `vagasdisponiveis` int(11) NOT NULL CHECK (`vagasdisponiveis` >= 0 and `vagasdisponiveis` <= `vagastotais`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `idaluno` int(11) NOT NULL,
  `nomealuno` varchar(50) NOT NULL,
  `telefonealuno` varchar(11) DEFAULT NULL,
  `idadealuno` int(11) DEFAULT NULL CHECK (`idadealuno` > 0),
  `fotoaluno` text DEFAULT NULL,
  `statusdematricula` varchar(20) DEFAULT 'Ativo' CHECK (`statusdematricula` in ('Ativo','Inativo','Trancado','Cancelado'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacaofisica`
--

CREATE TABLE `avaliacaofisica` (
  `idavaliacao` int(11) NOT NULL,
  `idalunoavaliado` int(11) NOT NULL,
  `peso` double DEFAULT NULL,
  `altura` double DEFAULT NULL CHECK (`altura` > 0 and `altura` < 3.0),
  `dataavaliacao` date DEFAULT NULL,
  `imc` double NOT NULL,
  `medidasdocorpo` text DEFAULT NULL,
  `percentualgordura` double DEFAULT NULL CHECK (`percentualgordura` between 0 and 100),
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avisos`
--

CREATE TABLE `avisos` (
  `idaviso` int(11) NOT NULL,
  `idaluno` int(11) NOT NULL,
  `tipoaviso` varchar(50) NOT NULL,
  `mensagem` text NOT NULL,
  `dataemissao` datetime DEFAULT current_timestamp(),
  `lido` tinyint(1) DEFAULT 0 CHECK (`lido` in (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `controle_presenca`
--

CREATE TABLE `controle_presenca` (
  `idpresenca` int(11) NOT NULL,
  `idhorarioaula` int(11) NOT NULL,
  `idalunopresente` int(11) NOT NULL,
  `presente` tinyint(1) DEFAULT 0 CHECK (`presente` in (0,1)),
  `datainscricao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `controle_presenca`
--
DELIMITER $$
CREATE TRIGGER `trgreservavaga` AFTER INSERT ON `controle_presenca` FOR EACH ROW BEGIN
    UPDATE agendaaulas
    SET vagasdisponiveis = vagasdisponiveis - 1
    WHERE idaula = NEW.idhorarioaula;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trgvalidarvagas` BEFORE INSERT ON `controle_presenca` FOR EACH ROW BEGIN
    IF (SELECT vagasdisponiveis FROM agendaaulas WHERE idaula = NEW.idhorarioaula) <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'A aula selecionada não possui mais vagas disponíveis.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `exerciciotreino`
--

CREATE TABLE `exerciciotreino` (
  `idexercicio` int(11) NOT NULL,
  `idfichatreino` int(11) NOT NULL,
  `grupomuscular` varchar(50) NOT NULL,
  `nomeexercicio` varchar(100) NOT NULL,
  `series` int(11) NOT NULL CHECK (`series` > 0),
  `repeticoes` int(11) NOT NULL CHECK (`repeticoes` > 0),
  `cargakg` double DEFAULT NULL CHECK (`cargakg` >= 0),
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fichatreino`
--

CREATE TABLE `fichatreino` (
  `idficha` int(11) NOT NULL,
  `idaluno` int(11) NOT NULL,
  `datacriacao` date DEFAULT curdate(),
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro`
--

CREATE TABLE `financeiro` (
  `idfatura` int(11) NOT NULL,
  `idaluno` int(11) NOT NULL,
  `idplano` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL CHECK (`valor` >= 0),
  `datavencimento` date NOT NULL,
  `datapagamento` date DEFAULT NULL,
  `statuspagamento` varchar(20) DEFAULT 'Pendente' CHECK (`statuspagamento` in ('Pendente','Pago','Atrasado','Cancelado'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Acionadores `financeiro`
--
DELIMITER $$
CREATE TRIGGER `trgavisofinanceiroinsert` AFTER INSERT ON `financeiro` FOR EACH ROW BEGIN
    IF NEW.statuspagamento = 'Pendente'
       AND NEW.datavencimento <= DATE_ADD(CURDATE(), INTERVAL 3 DAY) THEN
        INSERT INTO avisos (idaluno, tipoaviso, mensagem)
        VALUES (
            NEW.idaluno,
            'Mensalidade próxima do vencimento',
            CONCAT('Sua fatura no valor de R$ ', NEW.valor, ' vence em ',
                   DATE_FORMAT(NEW.datavencimento, '%d/%m/%Y'))
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trgavisofinanceiroupdate` AFTER UPDATE ON `financeiro` FOR EACH ROW BEGIN
    IF NEW.statuspagamento = 'Pendente'
       AND NEW.datavencimento <= DATE_ADD(CURDATE(), INTERVAL 3 DAY) THEN
        INSERT INTO avisos (idaluno, tipoaviso, mensagem)
        VALUES (
            NEW.idaluno,
            'Mensalidade próxima do vencimento',
            CONCAT('Sua fatura no valor de R$ ', NEW.valor, ' vence em ',
                   DATE_FORMAT(NEW.datavencimento, '%d/%m/%Y'))
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `idplano` int(11) NOT NULL,
  `nomeplano` varchar(50) NOT NULL,
  `valor` decimal(10,2) NOT NULL CHECK (`valor` >= 0),
  `duracaodias` int(11) NOT NULL CHECK (`duracaodias` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendaaulas`
--
ALTER TABLE `agendaaulas`
  ADD PRIMARY KEY (`idaula`),
  ADD KEY `idalunoaula` (`idalunoaula`);

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`idaluno`);

--
-- Índices de tabela `avaliacaofisica`
--
ALTER TABLE `avaliacaofisica`
  ADD PRIMARY KEY (`idavaliacao`),
  ADD KEY `idalunoavaliado` (`idalunoavaliado`);

--
-- Índices de tabela `avisos`
--
ALTER TABLE `avisos`
  ADD PRIMARY KEY (`idaviso`),
  ADD KEY `idaluno` (`idaluno`);

--
-- Índices de tabela `controle_presenca`
--
ALTER TABLE `controle_presenca`
  ADD PRIMARY KEY (`idpresenca`),
  ADD KEY `idhorarioaula` (`idhorarioaula`),
  ADD KEY `idalunopresente` (`idalunopresente`);

--
-- Índices de tabela `exerciciotreino`
--
ALTER TABLE `exerciciotreino`
  ADD PRIMARY KEY (`idexercicio`),
  ADD KEY `idfichatreino` (`idfichatreino`);

--
-- Índices de tabela `fichatreino`
--
ALTER TABLE `fichatreino`
  ADD PRIMARY KEY (`idficha`),
  ADD KEY `idaluno` (`idaluno`);

--
-- Índices de tabela `financeiro`
--
ALTER TABLE `financeiro`
  ADD PRIMARY KEY (`idfatura`),
  ADD KEY `idaluno` (`idaluno`),
  ADD KEY `idplano` (`idplano`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`idplano`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendaaulas`
--
ALTER TABLE `agendaaulas`
  MODIFY `idaula` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `idaluno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `avaliacaofisica`
--
ALTER TABLE `avaliacaofisica`
  MODIFY `idavaliacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `avisos`
--
ALTER TABLE `avisos`
  MODIFY `idaviso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `controle_presenca`
--
ALTER TABLE `controle_presenca`
  MODIFY `idpresenca` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `exerciciotreino`
--
ALTER TABLE `exerciciotreino`
  MODIFY `idexercicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fichatreino`
--
ALTER TABLE `fichatreino`
  MODIFY `idficha` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `financeiro`
--
ALTER TABLE `financeiro`
  MODIFY `idfatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `idplano` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendaaulas`
--
ALTER TABLE `agendaaulas`
  ADD CONSTRAINT `agendaaulas_ibfk_1` FOREIGN KEY (`idalunoaula`) REFERENCES `alunos` (`idaluno`);

--
-- Restrições para tabelas `avaliacaofisica`
--
ALTER TABLE `avaliacaofisica`
  ADD CONSTRAINT `avaliacaofisica_ibfk_1` FOREIGN KEY (`idalunoavaliado`) REFERENCES `alunos` (`idaluno`) ON DELETE CASCADE;

--
-- Restrições para tabelas `avisos`
--
ALTER TABLE `avisos`
  ADD CONSTRAINT `avisos_ibfk_1` FOREIGN KEY (`idaluno`) REFERENCES `alunos` (`idaluno`) ON DELETE CASCADE;

--
-- Restrições para tabelas `controle_presenca`
--
ALTER TABLE `controle_presenca`
  ADD CONSTRAINT `controle_presenca_ibfk_1` FOREIGN KEY (`idhorarioaula`) REFERENCES `agendaaulas` (`idaula`),
  ADD CONSTRAINT `controle_presenca_ibfk_2` FOREIGN KEY (`idalunopresente`) REFERENCES `alunos` (`idaluno`);

--
-- Restrições para tabelas `exerciciotreino`
--
ALTER TABLE `exerciciotreino`
  ADD CONSTRAINT `exerciciotreino_ibfk_1` FOREIGN KEY (`idfichatreino`) REFERENCES `fichatreino` (`idficha`) ON DELETE CASCADE;

--
-- Restrições para tabelas `fichatreino`
--
ALTER TABLE `fichatreino`
  ADD CONSTRAINT `fichatreino_ibfk_1` FOREIGN KEY (`idaluno`) REFERENCES `alunos` (`idaluno`) ON DELETE CASCADE;

--
-- Restrições para tabelas `financeiro`
--
ALTER TABLE `financeiro`
  ADD CONSTRAINT `financeiro_ibfk_1` FOREIGN KEY (`idaluno`) REFERENCES `alunos` (`idaluno`) ON DELETE CASCADE,
  ADD CONSTRAINT `financeiro_ibfk_2` FOREIGN KEY (`idplano`) REFERENCES `planos` (`idplano`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

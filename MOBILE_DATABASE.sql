CREATE DATABASE TCC_StudioFuncional_ThiagoRiato;

PRAGMA foreign_keys = ON;

CREATE TABLE alunos (
    idaluno INTEGER PRIMARY KEY AUTOINCREMENT,
    nomealuno VARCHAR (50) NOT NULL,
    telefonealuno VARCHAR (11),
    idadealuno INTEGER CHECK (idadealuno > 0),
    fotoaluno TEXT,
    statusdematricula TEXT DEFAULT 'Ativo' CHECK (statusdematricula IN ('Ativo', 'Inativo', 'Trancado', 'Cancelado'))
);

CREATE TABLE avaliacaofisica (
    idavaliacao INTEGER PRIMARY KEY AUTOINCREMENT,
    idalunoavaliado INTEGER NOT NULL,
    peso REAL,
    altura REAL,
    dataavaliacao DATE ,
    imc REAL NOT
    NULL CHECK (altura > 0 and altura <3.0)
    FOREIGN KEY (idalunoavaliado) REFERENCES alunos(idaluno) ON DELETE CASCADE
    medidasdocorpo TEXT,
    percentualgordura REAL CHECK (percentualgordura BETWEEN 0 AND 100),
    observacoes TEXT,
    FOREIGN KEY (idalunoavaliado) REFERENCES alunos (idaluno) ON DELETE CASCADE
);

CREATE TABLE agendaaulas(
    idaula INTEGER PRIMARY KEY AUTOINCREMENT,
    idalunoaula INTEGER NOT NULL,
    descricaoaula TEXT NOT NULL,
    datahorainicio TEXT NOT NULL,
    datahorafim TEXT NOT NULL,
    vagastotais INTEGER NOT NULL CHECK (vagastotais > 0),
    vagasdisponiveis INTEGER NOT NULL CHECK (vagasdisponiveis >= 0 AND vagasdisponiveis <= vagastotais)
    FOREIGN KEY (idalunoaula) REFERENCES alunos(idaluno)
);

CREATE TABLE fichatreino(
    idficha INTEGER PRIMARY KEY AUTOINCREMENT,
    idaluno INTEGER NOT NULL,
    datacriacao TEXT DEFAULT (DATE('now')),
    observacoes TEXT,
    FOREIGN KEY (idaluno) REFERENCES alunos(idaluno) ON DELETE CASCADE
);
CREATE TABLE controle_presenca (
idpresenca INTEGER PRIMARY KEY AUTOINCREMENT,
idhorarioaula INTEGER NOT NULL,
idalunopresente INTEGER NOT NULL,
presente INTEGER DEFAULT 0 CHECK (presente IN (0, 1)),
datainscricao TEXT DEFAULT (DATETIME('now', 'localtime')),
FOREIGN KEY (idhorarioaula) REFERENCES agendaaulas(idaula),
FOREIGN KEY (idalunopresente) REFERENCES alunos(idaluno),
);

CREATE TABLE exerciciotreino(
    idexercicio INTEGER PRIMARY KEY AUTOINCREMENT,
    idfichatreino INTEGER NOT NULL,
    grupomuscular TEXT NOT NULL,
    nomeexercicio TEXT NOT NULL,
    series INTEGER NOT NULL CHECK (series > 0),
    repeticoes INTEGER NOT NULL CHECK (repeticoes > 0),
    cargakg REAL CHECK (cargakg >= 0),
    observacoes TEXT,
    FOREIGN KEY (idfichatreino) REFERENCES fichatreino(idficha) ON DELETE CASCADE
);

CREATE TABLE planos (
    idplano INTEGER PRIMARY KEY AUTOINCREMENT,
    nomeplano TEXT NOT NULL,
    valor REAL NOT NULL CHECK (valor >= 0),
    duracaodias INTEGER NOT NULL CHECK (duracaodias > 0)
);

CREATE TABLE finaceiro(
    idfatura INTEGER PRIMARY KEY AUTOINCREMENT,
    idaluno  INTEGER NOT NULL,
    idplano INTEGER,
    valor REAL NOT NULL CHECK (valor >= 0),
    datavencimento TEXT NOT NULL,
    datapagamento TEXT,
    statuspagamento TEXT DEFAULT 'Pendente' CHECK (statuspagamento IN ('Pendente','Pago','Atrasado','Cancelado'))
    FOREIGN KEY (idaluno) REFERENCES alunos(idaluno) ON DELETE CASCADE
    FOREIGN KEY (idplano) REFERENCES planos(idplano) ON DELETE SET NULL
);

CREATE TABLE avisos(
    idaviso INTEGER PRIMARY KEY AUTOINCREMENT,
    idaluno INTEGER NOT NULL,
    tipoaviso TEXT NOT NULL,
    mensagem TEXT NOT NULL,
    dataemissao TEXT DEFAULT (DATETIME('now','localtime')),
    lido INTEGER DEFAULT 0 CHECK (lido IN (0,1)),
    FOREIGN KEY (idaluno) REFERENCES alunos(idaluno) ON DELETE CASCADE
);

DELETE FROM agendaaulas
WHERE datahorafim < DATETIME('now', 'localtime');

DELETE FROM financeiro
WHERE statuspagamento = 'Atrasado'
AND datavencimento < DATE('now', '-90 days');

PRAGMA foreign_keys = ON;

DELETE FROM alunos 
WHERE idaluno = 1;

CREATE TRIGGER trgvalidarvagas
BEFORE INSERT ON controlepresenca
FOR EACH ROW
BEGIN
    SELECT CASE
        WHEN (SELECT vagasdisponiveis FROM agendaaulas WHERE idaula = NEW.idaula) <= 0
        THEN RAISE(ABORT, 'A aula selecionada não possui mais vagas disponíveis.')
    END;
END;

CREATE TRIGGER trgreservavaga
AFTER INSERT ON controlepresenca
FOR EACH ROW
BEGIN
    UPDATE agendaaulas
    SET vagasdisponiveis = vagasdisponiveis - 1
    WHERE idaula = NEW.idaula;
END;

CREATE TRIGGER trgavisofinanceiroinsert
AFTER INSERT ON financeiro
FOR EACH ROW
WHEN NEW.statuspagamento = 'Pendente' AND NEW.datavencimento <= DATE('now', '+3 days')
BEGIN
    INSERT INTO avisos (idaluno, tipoaviso, mensagem)
    VALUES (
        NEW.id_aluno,
        'Mensalidade próxima do vencimento',
        'Sua fatura no valor de R$ ' || NEW.valor || ' vence em ' || STRFTIME('%d/%m/%Y', NEW.datavencimento)
    );
END;

CREATE TRIGGER trgavisofinanceiroupdate
AFTER UPDATE ON financeiro
FOR EACH ROW
WHEN NEW.statuspagamento = 'Pendente' AND NEW.datavencimento <= DATE('now', '+3 days')
BEGIN
    INSERT INTO avisos (idaluno, tipoaviso, mensagem)
    VALUES (
        NEW.idaluno,
        'Mensalidade próxima do vencimento',
        'Sua fatura no valor de R$ ' || NEW.valor || ' vence em ' || STRFTIME('%d/%m/%Y', NEW.datavencimento)
    );
END;

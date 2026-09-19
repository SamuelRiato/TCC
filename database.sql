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
);

CREATE TABLE fichatreino(
    idficha INTEGER PRIMARY KEY AUTOINCREMENT,
    idaluno INTEGER NOT NULL,
    datacriacao TEXT DEFAULT (DATE('now')),
    observacoes TEXT,
    FOREIGN KEY (idaluno) REFERENCES alunos(idaluno) ON DELETE CASCADE
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

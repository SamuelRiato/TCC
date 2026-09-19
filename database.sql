CREATE DATABASE TCC_StudioFuncional_ThiagoRiato;

PRAGMA foreign_keys = ON;

CREATE TABLE alunos (
    idaluno INTEGER PRIMARY KEY AUTOINCREMENT,
    nomealuno TEXT NOT NULL,
    telefonealuno TEXT,
    idadealuno INTEGER CHECK (idadealuno > 0),
    fotoaluno TEXT,
    statusdematricula TEXT DEFAULT 'Ativo' CHECK (statusdematricula IN ('Ativo', 'Inativo', 'Trancado', 'Cancelado'))
);

CREATE TABLE avaliacaofisica (
    idavaliacao INTEGER PRIMARY KEY AUTOINCREMENT,
    idalunoavaliado INTEGER NOT NULL,
    peso REAL,
    altura REAL,
    dataavaliacao TEXT DEFAULT (DATE('now')),
    peso REAL NOT
    NULL CHECK (altura > 0 and altura <3.0)
    FOREIGN KEY (idalunoavaliado) REFERENCES alunos(idaluno) ON DELETE CASCADE
);

CREATE DATABASE TCC_StudioFuncional_ThiagoRiato;

CREATE table alunos(
    idaluno int auto_increment not null primary key,
    nomealuno varchar (100) not null,
    telefone varchar(20),
    idade int check (idade > 0),
    fotoaluno varchar (255),
    statusdematricula VARCHAR(20) DEFAULT 'Ativo' CHECK (statusdematricula IN ('Ativo', 'Inativo', 'Trancado', 'Cancelado')),
     datadocadastro date)
<?php
declare(strict_types=1);

const APP_NOME = 'Thiago Riato Personal Studio';
const STORAGE_FILE = __DIR__ . '/../storage/users.json';
const AVATAR_DIR = __DIR__ . '/../public/uploads/avatars/';
const META_SEMANAL = 5;

const OBJETIVOS = [
    'Ganho de massa muscular',
    'Emagrecimento',
    'Condicionamento físico',
    'Saúde e bem-estar',
    'Reabilitação',
];

const DIAS = [
    1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta',
    5 => 'Sexta',   6 => 'Sábado', 7 => 'Domingo',
];

// Agenda semanal do estúdio (1 = segunda ... 7 = domingo). Edite à vontade.
const AGENDA = [
    1 => ['treino' => 'Membros Superiores (A)', 'hora' => '18:00'],
    2 => ['treino' => 'Cardio: Corrida e Bike',  'hora' => '18:00'],
    3 => ['treino' => 'Membros Inferiores (B)', 'hora' => '18:00'],
    4 => ['treino' => 'Cardio: HIIT',            'hora' => '18:00'],
    5 => ['treino' => 'Full Body (C)',           'hora' => '18:00'],
];

// Catálogo de treinos exibido em "Meus Treinos" e na página Treinos.
const TREINOS = [
    'musculacao' => [
        'titulo'    => 'Musculação',
        'descricao' => 'Fichas de Treino A, B e C',
        'icone'     => 'dumbbell',
        'itens'     => [
            ['Ficha A', 'Membros Superiores'],
            ['Ficha B', 'Membros Inferiores'],
            ['Ficha C', 'Full Body'],
        ],
    ],
    'cardio' => [
        'titulo'    => 'Cardio',
        'descricao' => 'Corrida, Bike e HIIT pré-treino',
        'icone'     => 'heart-pulse',
        'itens'     => [
            ['Corrida', 'Base aeróbica'],
            ['Bike', 'Baixo impacto'],
            ['HIIT pré-treino', 'Intervalos curtos'],
        ],
    ],
];

<?php

use App\Http\Controllers\OpenAIController;
use Illuminate\Support\Facades\Route;

/**
 * Define a rota POST para enviar o prompt e gerar a resposta da IA.
 *
 * A rota '/generate-prompt' utiliza o método 'generateResponse' do 'OpenAIController' 
 * para processar a requisição POST. Esta rota é chamada quando o usuário envia o 
 * formulário de prompt. 
 *
 * Nome da rota: 'generate.prompt' - Isso permite referenciar a rota pelo nome, 
 * o que torna o código mais legível e fácil de manter.
 */
Route::post('/generate-prompt', [OpenAIController::class, 'generateResponse'])->name('generate.prompt');

/**
 * Define a rota GET para exibir o formulário de entrada e os dados.
 *
 * A rota '/generate-prompt' utiliza o método 'showForm' do 'OpenAIController' 
 * para exibir o formulário onde o usuário pode inserir o prompt. 
 * Esse formulário também apresenta os dados consultados no banco, que 
 * serão enviados para análise pela IA.
 *
 * Tipo de requisição: GET - Usado para exibir a página de entrada sem alterar dados.
 */
Route::get('/generate-prompt', [OpenAIController::class, 'showForm']);

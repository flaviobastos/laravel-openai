<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OpenAIController extends Controller
{

    /**
     * Exibe o formulário para o usuário, carregando dados da tabela 'datas' para exibição.
     *
     * Esta função consulta o banco de dados, recupera todos os registros da tabela 'datas' 
     * e os envia para a view 'generate_prompt', onde os dados serão apresentados ao usuário.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        // Consulta o banco e obtém todos os registros da tabela 'datas'
        $data = DB::table('datas')->get(); // Substitua 'datas' pelo nome da sua tabela

        // Retorna a view 'generate_prompt' com os dados consultados
        return view(
            'generate_prompt',
            [
                'data' => $data
            ]
        );
    }

    /**
     * Processa o prompt do usuário e envia os dados para análise pela API da OpenAI.
     *
     * Esta função recebe o prompt do usuário, converte os dados da tabela 'datas' em uma string,
     * e os combina com o prompt antes de enviar a solicitação à API. A resposta da IA é então
     * retornada à view junto com a contagem de tokens usados.
     *
     * @param \Illuminate\Http\Request $request A requisição HTTP contendo o prompt do usuário.
     * @return \Illuminate\View\View A view 'generate_prompt' com a resposta da IA e dados adicionais.
     */
    public function generateResponse(Request $request)
    {
        // Recebe o prompt (pergunta) enviado pelo usuário no formulário
        $prompt = $request->input('prompt');

        // Consulta o banco de dados para obter todos os registros da tabela 'datas'
        $data = DB::table('datas')->get();

        // Converte os dados da tabela 'datas' em uma string formatada para enviar à IA
        $dataString = "Dados:\n";
        foreach ($data as $row) {
            $dataString .= "Nome: {$row->nome}, Gênero: {$row->genero}, Idade: {$row->idade}, Altura: {$row->altura}, Glicose: {$row->glicose}, Colesterol Total: {$row->colesterol_total}, Peso: {$row->peso}\n";
        }

        // Combina o prompt do usuário com os dados formatados
        $fullPrompt = "{$dataString}\n\nPergunta: {$prompt}";

        // Configura a requisição HTTP para enviar os dados à API da OpenAI
        $response = Http::withOptions(['verify' => false]) // Desativa a verificação SSL para simplificar em desenvolvimento
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('openai.openai.key'), // Define o token de autorização da OpenAI
                'Content-Type' => 'application/json' // Define o tipo de conteúdo como JSON
            ])
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini', // Modelo da IA utilizado para análise (GPT-4o Mini)
                'messages' => [
                    [
                        'role' => 'system', // Papel do sistema configurando o contexto para a IA
                        'content' => 'Analise os dados fornecidos e responda a pergunta de acordo com as informações na tabela fornecida, sendo conciso, sem cálculos, mostrando apenas os resultados.'
                    ],
                    [
                        'role' => 'user', // Mensagem do usuário com o prompt e dados para análise
                        'content' => $fullPrompt
                    ]
                ],
                'max_tokens' => 150, // Define o limite máximo de tokens para a resposta
                'temperature' => 1, // Define a criatividade da resposta, 0 para respostas mais diretas
            ]);

        // Processa a resposta da API e verifica se foi retornado conteúdo
        $responseData = $response->json();

        if (isset($responseData['choices'][0]['message']['content'])) {
            // Extrai o conteúdo da resposta
            $responseText = $responseData['choices'][0]['message']['content'];

            // Extrai a contagem de tokens usados, se disponível
            $promptTokens = $responseData['usage']['prompt_tokens'] ?? 0;
            $completionTokens = $responseData['usage']['completion_tokens'] ?? 0;
            $totalTokens = $responseData['usage']['total_tokens'] ?? 0;
        } else {
            // Caso haja erro, define a mensagem de erro padrão
            $responseText = $responseData['error']['message'] ?? 'Erro ao obter resposta da API.';
            $promptTokens = $completionTokens = $totalTokens = 0; // Define valores de tokens como zero
        }

        // Retorna a view 'generate_prompt' com a resposta da IA, contagem de tokens e dados da tabela
        return view('generate_prompt', [
            'prompt' => $prompt, // Prompt enviado pelo usuário
            'response' => $responseText, // Resposta gerada pela IA
            'data' => $data, // Dados da tabela 'datas' para exibição
            'promptTokens' => $promptTokens, // Tokens usados pelo prompt
            'completionTokens' => $completionTokens, // Tokens usados pela resposta
            'totalTokens' => $totalTokens, // Tokens totais utilizados
        ]);
    }
}

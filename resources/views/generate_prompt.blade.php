<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerar Resposta com OpenAI</title>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 640px;
            margin: 40px;
        }

        td,
        th {
            border: 1px solid #000000;
            text-align: center;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        input {
            width: 300px;
        }
    </style>
</head>

<body>

    <h2>Dados da tabela:</h2>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Gênero</th>
                <th>Idade</th>
                <th>Altura</th>
                <th>Glicose</th>
                <th>Colesterol</th>
                <th>Peso</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row->nome }}</td>
                    <td>{{ $row->genero }}</td>
                    <td>{{ $row->idade }}</td>
                    <td>{{ $row->altura }}</td>
                    <td>{{ $row->glicose }}</td>
                    <td>{{ $row->colesterol_total }}</td>
                    <td>{{ $row->peso }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form action="{{ route('generate.prompt') }}" method="POST">
        @csrf
        <label for="prompt">Digite a pergunta:</label>
        <input type="text" id="prompt" name="prompt" required>
        <button type="submit">Gerar Resposta</button>
    </form>

    @if (isset($response))
        <h3>Pergunta:</h3>
        <p>{{ $prompt }}</p>
        <h3>Resposta da AI:</h3>
        <p>{{ $response }}</p>
        <h4>Tokens Utilizados:</h4>
        <p>Tokens da Pergunta: {{ $promptTokens }}</p>
        <p>Tokens da Resposta: {{ $completionTokens }}</p>
        <p>Tokens Totais: {{ $totalTokens }}</p>
    @endif
</body>

</html>

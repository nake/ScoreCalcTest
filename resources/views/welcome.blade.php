<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <tournament-title>Match Calculator</tournament-title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            td {
                border: 1px solid black;
            }
            .group-a-container,
            .group-b-container {
                width: 50%;
                float: left;
            }

            .button-container {
                float: left;
                width: 100%;
            }

            .conclusion-container {
                width: 100%;
                float: left;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .quarter-finals-container,
            .semi-finals-container,
            .finals-container {
                width: 20%;
                float: left;
            }

            .team-one,
            .team-two {
                float: left;
                width: 80%;
            }

            .team-one {
                border-bottom: 1px solid black;
            }

            .result {
                float: right;
                width: 15%;
            }

            .match-container {
                width: 100%;
                float: left;
                margin-bottom: 30px;
            }

            .tournament-title {
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="button-container">
            <button onclick="window.location='{{ url("/prepareGroupA") }}'">Generate Group A</button>
            <button onclick="window.location='{{ url("/prepareGroupB") }}'">Generate Group B</button>
            <button onclick="window.location='{{ url("/concludeTournament") }}'">Generate Conclusion</button>
        </div>
        @if (!empty($groupA))
            <div class="group-a-container">
                <span class="tournament-title">Group A</span>
                <table>
                    <tr>
                        <td class="tournament-title">Teams</td>
                        @foreach ($groupA as $teamName => $result)
                            <td>{{ $teamName }}</td>
                        @endforeach
                        <td class="tournament-title">Score</td>
                    </tr>
                    @foreach ($groupA as $teamAName => $result)
                        <tr>
                            <td>{{ $teamAName }}</td>
                            @foreach ($groupA[$teamAName] as $result)
                                <td>{{ $result }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
        @if (!empty($groupB))
            <div class="group-b-container">
                <span class="tournament-title">Group B</span>
                <table>
                    <tr>
                        <td class="tournament-title">Teams</td>
                        @foreach ($groupB as $teamName => $result)
                            <td>{{ $teamName }}</td>
                        @endforeach
                        <td class="tournament-title">Score</td>
                    </tr>
                    @foreach ($groupB as $teamAName => $result)
                        <tr>
                            <td>{{ $teamAName }}</td>
                            @foreach ($groupB[$teamAName] as $result)
                                <td>{{ $result }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
        @if (!empty($conclusion))
            <div class="conclusion-container">
                <div class="quarter-finals-container">
                    <span class="tournament-title">Quarter Finals</span>
                    @foreach ($conclusion['quarterFinals'] as $result)
                        <div class="match-container">
                            <div class="team-one">{{ $result['teamA'] }}</div>
                            <div class="team-two">{{ $result['teamB'] }}</div>
                            <div class="result">{{ $result['result'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="semi-finals-container">
                    <span class="tournament-title">Semi Finals</span>
                    @foreach ($conclusion['semiFinals'] as $result)
                        <div class="match-container">
                            <div class="team-one">{{ $result['teamA'] }}</div>
                            <div class="team-two">{{ $result['teamB'] }}</div>
                            <div class="result">{{ $result['result'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="third-place-match-container">
                    <span class="tournament-title">Third place match</span>
                    <div class="match-container">
                        <div class="team-one">{{ $conclusion['thirdPlaceMatch']['teamA'] }}</div>
                        <div class="team-two">{{ $conclusion['thirdPlaceMatch']['teamB'] }}</div>
                        <div class="result">{{ $conclusion['thirdPlaceMatch']['result'] }}</div>
                    </div>
                </div>
                <div class="finals-container">
                    <span class="tournament-title">Finals</span>
                    <div class="match-container">
                        <div class="team-one">{{ $conclusion['finals']['teamA'] }}</div>
                        <div class="team-two">{{ $conclusion['finals']['teamB'] }}</div>
                        <div class="result">{{ $conclusion['finals']['result'] }}</div>
                    </div>
                </div>
                <div class="results-container">
                    <span class="tournament-title">Results</span>
                    <div class="match-container">
                        @foreach ($conclusion['result'] as $key => $result)
                            <div class="final-result">{{ $key }}. {{ $result }}
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </body>
</html>

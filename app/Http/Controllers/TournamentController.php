<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function init()
    {
        self::clearData(); // nuking everything on refresh, since persistence is not required
        return self::getDataAndView();
    }

    public function prepareGroupA()
    {
        $tournament = self::getOrInitTournament();
        if (!$tournament->groupAGenerated) {
            $teams = self::generateGroup('A');
            $tournament->setAttribute('groupAGenerated', true);
            $tournament->save();
        }
        return self::getDataAndView();
    }

    public function prepareGroupB()
    {
        $tournament = self::getOrInitTournament();
        if (!$tournament->groupBGenerated) {
            $teams = self::generateGroup('B');
            $tournament->setAttribute('groupBGenerated', true);
            $tournament->save();
        }
        return self::getDataAndView();
    }

    public function concludeTournament()
    {
        $tournament = self::getOrInitTournament();
        if (!$tournament->resultsGenerated) {
            $tournament->setAttribute('resultsGenerated', true);
            $tournament->save();
        }
        return self::getDataAndView();
    }

    private static function calculateTournament()
    {
        $tournament = self::getOrInitTournament();
        $topFourFromA = self::getTopFourFromGroup('A');
        $topFourFromB = self::getTopFourFromGroup('B');
        $quarterFinals = array();
        $semiFinals = array();
        $finals = array();
        $result = array();

        foreach ($topFourFromA as $key => $groupATeamId) {
            $offset = 3 - $key;
            $quarterFinals[] = self::generateMatchData($groupATeamId, $topFourFromB[$offset]);
        }
        $semiFinalsParticipants = array();

        foreach ($quarterFinals as $match) {
            $semiFinalsParticipants[] = $match['winnerTeamId'];
        }
        $semiFinals[] = self::generateMatchData($semiFinalsParticipants[0], $semiFinalsParticipants[1]);
        $semiFinals[] = self::generateMatchData($semiFinalsParticipants[2], $semiFinalsParticipants[3]);
        $finalists = array();
        $thirdPlaceMatchTeams = array();

        foreach ($semiFinals as $match) {
            $finalists[] = $match['winnerTeamId'];
            $thirdPlaceMatchTeams[] = $match['loserTeamId'];
        }
        $finals = self::generateMatchData($finalists[0], $finalists[1]);
        $thirdPlaceMatch = self::generateMatchData($thirdPlaceMatchTeams[0], $thirdPlaceMatchTeams[1]);
        $result = array();
        $result[1] = \App\Team::find($finals['winnerTeamId'])->name;
        $result[2] = \App\Team::find($finals['loserTeamId'])->name;
        $result[3] = \App\Team::find($thirdPlaceMatch['winnerTeamId'])->name;
        $result[4] = \App\Team::find($thirdPlaceMatch['loserTeamId'])->name;
        $tournament->setAttribute('resultsGenerated', true);
        $tournament->save();
        foreach ($result as $place => $winningTeamName) {
            $tournamentResult = factory(\App\Results::class)->create();
            $tournamentResult->setAttribute('teamName', $winningTeamName);
            $tournamentResult->setAttribute('place', $place);
            $tournamentResult->save();
        }

        return array(
            'quarterFinals' => $quarterFinals, 
            'semiFinals' => $semiFinals, 
            'thirdPlaceMatch' => $thirdPlaceMatch, 
            'finals' => $finals, 
            'result' => $result
        );
    }

    private static function generateMatchData(int $teamIdA, int $teamIdB)
    {
        $teamA = \App\Team::find($teamIdA);
        $teamB = \App\Team::find($teamIdB);
        $victory = $teamA->power > $teamB->power;
        return array(
            'teamA' => $teamA->name,
            'teamB' => $teamB->name,
            'result' => $victory ? '1:0' : '0:1',
            'winnerTeamId' => $victory ? $teamIdA : $teamIdB,
            'loserTeamId' => $victory ? $teamIdB : $teamIdA,
        );
    }

    private static function getTopFourFromGroup(string $groupName)
    {
        $resultArray = array();
        $results = self::getResults($groupName, true);
        foreach ($results as  $result) {
            $resultArray[$result['id']] = $result['score'];
        }
        $topFour = array();
        for ($i = 0; $i < 4; $i++) {
            $key = array_search(max($resultArray), $resultArray);
            $topFour[$i] = $key;
            unset($resultArray[$key]);
        }
        return $topFour;
    }

    private static function getDataAndView()
    {
        $resultsA = self::getResults('A');
        $resultsB = self::getResults('B');
        if (empty($resultsA)
            && empty($resultsB)) {
            return view('welcome');
        }
        
        $conclusion = array();
        if (self::getOrInitTournament()->groupAGenerated
            && self::getOrInitTournament()->groupBGenerated
            && self::getOrInitTournament()->resultsGenerated) {
            $conclusion = self::calculateTournament();
        }

        return view('welcome', [
            'groupA' => $resultsA, 
            'groupB' => $resultsB,
            'conclusion' => $conclusion
        ]);
    }

    private static function getResults(string $groupName, bool $includeId = false)
    {
        $teams = \App\Team::all()->filter(function ($team) use ($groupName) {
            return $team->group == $groupName;
        });
        $results = array();
        foreach ($teams as $team) {
            $score = 0;
            foreach ($teams as $opponentTeam) {
                if ($team->name === $opponentTeam->name) {
                    $results[$team->name][$opponentTeam->name] = 'blank';
                    continue;
                }
                $victory = $team->power > $opponentTeam->power;
                if ($victory) {
                    $score++;
                }
                $results[$team->name][$opponentTeam->name] =  $victory ? '1:0' : '0:1';
            }
            $results[$team->name]['score'] = $score;
            if ($includeId) {
                $results[$team->name]['id'] = $team->id;
            }
        }
        return $results;
    }

    private static function generateGroup(string $groupName)
    {
        $teams = factory(\App\Team::class, 8)->create();
        foreach ($teams as $team) {
            $team->setAttribute('group', $groupName);
            $team->save();
        }
    }

    private static function clearData()
    {
        \App\Team::truncate();
        \App\Tournament::truncate();
    }

    private static function getOrInitTournament()
    {
        $tournament = \App\Tournament::all();
        if ($tournament->isEmpty()) {
            return factory(\App\Tournament::class, 1)->create()->first();
        }
        return $tournament->first();
    }
}

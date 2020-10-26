<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws Exception
     */
    public function index()
    {
        $cards = $this->initialShuffle();

        $firstCard = $this->getFirstCard($cards);

        $model = $this->buildGameModel($firstCard);

        return view('game')->with('data', $model);
    }





    public function initialShuffle(): array
    {

        try {

            $response = Http::get('https://higher-lower.code23.com/api/deck');
            $cards = $response->json();
            $this->shuffle_assoc($cards);

            return $cards;

        } catch (Exception $e) {

            throw $e;

        }


    }


    public function shuffle_assoc(&$array) {
        $keys = array_keys($array);

        shuffle($keys);

        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;

        return true;
    }

    /**
     * @return mixed
     */
    public function getFirstCard(&$cards)
    {

        $firstCard =  array_shift($cards);
        session(['cards' => $cards]);
        return $firstCard;


    }

    /**
     * @param array $cards
     * @param $firstCard
     * @return Game
     */
    public function buildGameModel($firstCard): Game
    {
        $model = new Game();
        $model->firstCard = $firstCard;
        $model->health = 3;
        $model->score = 0;

        return $model;
    }
}

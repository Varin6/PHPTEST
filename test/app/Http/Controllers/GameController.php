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

        $model = $this->buildGameModel($firstCard, 3, 0);

        return view('game')->with('data', $model);
    }


    /**
     * Higher card has been chosen by user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws Exception
     */
    public function higher()
    {

        try {

            $cards = $this->getCardsFromSession();

            $currentCard = $this->getCurrentCardFromSession();


            $firstCard = $this->getFirstCard($cards);

            $model = $this->buildGameModel($firstCard);

            return view('game')->with('data', $model);

        } catch (Exception $e) {

            throw $e;

        }




    }


    public function lower(Request $request, array $cards)
    {


        try {

            $cards = $this->getCardsFromSession();

            $firstCard = $this->getFirstCard($cards);

            $model = $this->buildGameModel($firstCard);

            return view('game')->with('data', $model);

        } catch (Exception $e) {

            throw $e;

        }


    }




    /**
     * Fetch and an initial shuffle of cards
     * @return array
     * @throws Exception
     */
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


    /**
     * Shuffle array and keep key/value pairs - stolen from php.net comments
     * @param $array
     * @return bool
     */
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
     * Grab the first item from the array and remove it from the array (passing array as reference)
     * Store array in session once the first item was removed
     * @return mixed
     */
    public function getFirstCard(&$cards)
    {

        $firstCard =  array_shift($cards);

        session(['cards' => $cards]);
        session(['currentCard' => $firstCard]);

        return $firstCard;


    }

    /**
     * Build view model
     * @param $firstCard
     * @return Game
     */
    public function buildGameModel($firstCard, $health, $score): Game
    {
        $model = new Game();
        $model->firstCard = $firstCard;
        $model->health = $health;
        $model->score = $score;

        return $model;
    }

    /**
     * Grab cards from session if they exist
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     * @throws Exception
     */
    public function getCardsFromSession()
    {
        try {

            // check first if exists, as this is what the good guys do :)

            if (session()->has('cards')) {

                $cards = session('cards');
                return $cards;

            } else {

                throw new Exception("Session does not have cards array!");

            }

        } catch(Exception $e) {

            throw $e;        }


    }




    /**
     * Grab current card from session if they exist
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     * @throws Exception
     */
    public function getCurrentCardFromSession()
    {
        try {

            // check first if exists, as this is what the good guys do :)

            if (session()->has('currentCard')) {

                $cards = session('currentCard');
                return $cards;

            } else {

                throw new Exception("Session does not have cards array!");

            }

        } catch(Exception $e) {

            throw $e;

        }


    }

}

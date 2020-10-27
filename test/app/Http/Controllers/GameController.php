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

        $this->assignHealthAndScore(3, 0);

        $nextCard = $this->getNextCard($cards);

        $model = $this->buildGameModel($nextCard, 3, 0, "Good luck!");

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

            $cards = $this->getValueFromSession('cards');

            $currentCard = $this->getValueFromSession('currentCard');

            $health = $this->getValueFromSession('health');

            $score = $this->getValueFromSession('score');

            $nextCard = $this->getNextCard($cards);


            if ($health < 1) {

                $model = $this->buildGameModel($nextCard, $health, $score, "Restart the game, no point refreshing....");

                return view('game')->with('data', $model);
            }

            if ($this->isCardHigher($currentCard, $nextCard)) {

                $score = $this->incrementScore($score);

                $model = $this->buildGameModel($nextCard, $health, $score, "Great stuff, carry on!");

                return view('game')->with('data', $model);

            } else {

                if ($health <= 1) {

                    $health = $this->reduceHealth($health);

                    $model = $this->buildGameModel($nextCard, $health, $score, "That's it, you have failed miserably...");

                    return view('game')->with('data', $model);

                } else {

                    $health = $this->reduceHealth($health);

                    $model = $this->buildGameModel($nextCard, $health, $score, "You're one step closer to death...");

                    return view('game')->with('data', $model);

                }

            }


        } catch (Exception $e) {

            throw $e;

        }




    }


    /**
     * When user guesses lower card
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws Exception
     */
    public function lower()
    {


        try {

            $cards = $this->getValueFromSession('cards');

            $currentCard = $this->getValueFromSession('currentCard');

            $health = $this->getValueFromSession('health');

            $score = $this->getValueFromSession('score');

            $nextCard = $this->getNextCard($cards);

            if ($health < 1) {

                $model = $this->buildGameModel($nextCard, $health, $score, "Restart the game, no point refreshing....");

                return view('game')->with('data', $model);
            }

            if (!$this->isCardHigher($currentCard, $nextCard)) {

                $score = $this->incrementScore($score);

                $model = $this->buildGameModel($nextCard, $health, $score, "Great stuff, carry on!");

                return view('game')->with('data', $model);

            } else {

                if ($health <= 1) {

                    $health = $this->reduceHealth($health);

                    $model = $this->buildGameModel($nextCard, $health, $score, "That's it, you have failed miserably...");

                    return view('game')->with('data', $model);

                } else {

                    $health = $this->reduceHealth($health);

                    $model = $this->buildGameModel($nextCard, $health, $score, "You're one step closer to death...");

                    return view('game')->with('data', $model);

                }

            }


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
    public function getNextCard(&$cards)
    {

        $nextCard =  array_shift($cards);

        session(['cards' => $cards]);
        session(['currentCard' => $nextCard]);

        return $nextCard;


    }


    /**
     * Assign health and score
     * @return mixed
     */
    public function assignHealthAndScore($health, $score)
    {

        session(['health' => $health]);
        session(['score' => $score]);

        return true;

    }


    /**
     * increment score
     * @param $score
     * @return mixed
     */
    public function incrementScore($score)
    {

        $score = $score + 1;
        session(['score' => $score]);

        return $score;

    }


    /**
     * Reduce health
     * @param $score
     * @return mixed
     */
    public function reduceHealth($health)
    {

        $health = $health - 1;
        session(['health' => $health]);

        return $health;

    }



    /**
     * Build view model
     * @param $nextCard
     * @return Game
     */
    public function buildGameModel($nextCard, $health, $score, $message): Game
    {
        $model = new Game();
        $model->nextCard = $nextCard;
        $model->health = $health;
        $model->score = $score;
        $model->message = $message;

        return $model;
    }

    /**
     * Grab cards from session if they exist
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     * @throws Exception
     */

    public function getValueFromSession($string)
    {
        try {

            // check first if exists, as this is what the good guys do :)

            if (session()->has($string)) {

                $value = session($string);
                return $value;

            } else {

                throw new Exception("Session does not have cards array!");

            }

        } catch(Exception $e) {

            throw $e;        }


    }



    /**
     * Check if next card is higher
     * First check the values, if that produces a match - compare the suites next. Those can't equal each other as we only have one deck.
     *
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     * @throws Exception
     */
    public function isCardHigher($currentCard, $nextCard)
    {
        try {


            if ($this->assignValueScore($currentCard['value']) > $this->assignValueScore($nextCard['value'])) {

                return false;

            }

            if ($this->assignValueScore($currentCard['value']) < $this->assignValueScore($nextCard['value'])) {

                return true;

            }


            if ($this->assignValueScore($currentCard['value']) == $this->assignValueScore($nextCard['value'])) {

                if ($this->assignSuitScore($currentCard['suit']) < $this->assignSuitScore($nextCard['suit'])) {

                    return true;

                } else {

                    return false;

                }

            }


        } catch(Exception $e) {

            throw $e;

        }


    }




    /**
     * Check if next card is lower
     * First check the values, if that produces a match - compare the suites next. Those can't equal each other as we only have one deck.
     *
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     * @throws Exception
     */
    public function isCardLower($currentCard, $nextCard)
    {
        try {


            if ($this->assignValueScore($currentCard['value']) > $this->assignValueScore($nextCard['value'])) {

                return true;

            }

            if ($this->assignValueScore($currentCard['value']) < $this->assignValueScore($nextCard['value'])) {

                return false;

            }


            if ($this->assignValueScore($currentCard['value']) == $this->assignValueScore($nextCard['value'])) {

                if ($this->assignSuitScore($currentCard['suit']) > $this->assignSuitScore($nextCard['suit'])) {

                    return true;

                } else {

                    return false;

                }

            }


        } catch(Exception $e) {

            throw $e;

        }


    }



    /**
     * Asigns value to the card
     * @param $cardValue
     * @return int
     */
    public function assignValueScore($cardValue) {

        switch (strtolower($cardValue)) {
            case "a":
                return 1;
            case "2":
                return 2;
            case "3":
                return 3;
            case "4":
                return 4;
            case "5":
                return 5;
            case "6":
                return 6;
            case "7":
                return 7;
            case "8":
                return 8;
            case "9":
                return 9;
            case "10":
                return 10;
            case "j":
                return 11;
            case "q":
                return 12;
            case "k":
                return 13;
            default:
                return 0;
        }
    }


    /**
     * Asign score based on card suit
     * @param $cardValue
     * @return int
     */
    public function assignSuitScore($cardValue) {

        switch (strtolower($cardValue)) {
            case "clubs":
                return 1;
            case "spades":
                return 2;
            case "diamonds":
                return 3;
            case "hearts":
                return 4;
            default:
                return 0;
        }
    }

}

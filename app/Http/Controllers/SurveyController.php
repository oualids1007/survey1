<?php

namespace App\Http\Controllers;

use App\ProtectedUrl;
use App\Survey;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SurveyController extends Controller
{
    public function home(Request $request)
    {
        $surveys = Survey::get();
        return view('home', compact('surveys'));
    }

    # Show page to create new survey
    public function new_survey()
    {
        return view('survey.new');
    }

    public function create(Request $request, Survey $survey)
    {
        $arr = $request->all();
        // $request->all()['user_id'] = Auth::id();
        $arr['user_id'] = Auth::id();

        $surveyItem = $survey->create($arr);

        $protectedUrl = new ProtectedUrl();
        $protectedUrl->survey_id = $surveyItem->id;
        $protectedUrl->url = md5(time());

        $protectedUrl->save();

        return Redirect::to("/survey/{$surveyItem->id}");
    }

    # retrieve detail page and add questions here
    public function detail_survey(Survey $survey)
    {
        $data = [];
        $survey->load('questions.user');

        if ($survey->protected_urls->first() !== null) {
            $data['url'] = $survey->protected_urls->first();
        }
        $data['survey'] = $survey;
        return view('survey.detail', $data);
    }


    public function edit(Survey $survey)
    {
        return view('survey.edit', compact('survey'));
    }

    # edit survey
    public function update(Request $request, Survey $survey)
    {
        $survey->update($request->only(['title', 'description']));
        return redirect()->action('SurveyController@detail_survey', [$survey->id]);
    }

    # view survey publicly and complete survey
    public function view_survey(Survey $survey)
    {
        $survey->option_name = unserialize($survey->option_name);
        return view('survey.view', compact('survey'));
    }

    # view submitted answers from current logged in user
    public function view_survey_answers(Survey $survey)
    {
        $survey->load('user.questions.answers');
        // return view('survey.detail', compact('survey'));
        // return $survey;
        return view('answer.view', compact('survey'));
    }

    // TODO: Make sure user deleting survey
    // has authority to
    public function delete_survey(Survey $survey)
    {
        $this->middleware('auth');
        $survey->delete();
        return redirect('');
    }

    public function show_protected_survey($hash)
    {
        $survey = ProtectedUrl::where('url', $hash)->first()->survey;
        $survey->load('questions.user');
        $url = $survey->protected_urls->first();
        return view('survey.detail', ['survey' => $survey, 'url' => $url]);

    }

 public function delete_survey(Survey $survey)
  {
    $survey->delete();
    return redirect('');
  }
}

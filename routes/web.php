<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;



$app->match('/', function (Request $request) use ($app) {

    $query = "SELECT * FROM recalls";

    if(!empty($request->get('select')))
    {
        if($request->get('select') === 'ascending')
        {
            $query = "SELECT * FROM recalls ORDER BY likes DESC,created_at DESC";
        }
        if ($request->get('select') === 'descending'){
            $query = "SELECT * FROM recalls ORDER BY likes,created_at";
        }
        if ($request->get('select' === 'default'))
        {
            $query = "SELECT * FROM recalls";
        }
    }

    $recalls = $app['db']->prepare("$query");

    $recalls->execute();

    $recalls = $recalls->fetchAll(\PDO::FETCH_CLASS, \AI\Models\Recall::class);


    $count = count($recalls);
    $count--;

    for($k = 0; $k <= $count; $k++)
    {
        foreach ($recalls[$k] as $recall)
        {
            $id = $recalls[$k]->id;
            $author = $recalls[$k]->author;
            $text = $recalls[$k]->text;
            $created_at = $recalls[$k]->created_at;
            $likes = $recalls[$k]->likes;

            $results[] = [
                'id' => $id,
                'author' => $author,
                'text' => $text,
                'created_at' => $created_at,
                'likes' => $likes,
            ];
            break;
        }
    }

    return $app['twig']->render('index.html.twig', array('results' => $results,'count' => $count));
})->bind('ListRecall');

$app->get('/404',function () use ($app)
{
    return $app['twig']->render('errors/404.html.twig', array());
})->bind('404');

$app->match('/add', function (Request $request) use ($app, $form){

    if(!empty($request->get('add')))
    {
        $author = $request->get('name');
        $text = $request->get('text');
        $ip_author = $_SERVER['REMOTE_ADDR'];

        $add = $app['db']->prepare("INSERT INTO recalls (text, author, ip_author, created_at) VALUES ('$text', '$author', INET_ATON('$ip_author'), NOW());");

        $add->execute();

        return $app->redirect($app["url_generator"]->generate("ListRecall"));
    }else{
        return $app['twig']->render('addRecall.html.twig', array());
    }

})->bind('AddRecall');

$app->match('/moreRecall/{id}', function (Request $request,$id) use ($app){

    $recallsAll = $app['db']->prepare("SELECT id FROM recalls");
    $recallsAll->execute();
    $recallsAll = $recallsAll->fetchAll(\PDO::FETCH_CLASS, \AI\Models\Recall::class);

    $count = count($recallsAll);
    $count--;

    for($k = 0; $k <= $count; $k++)
    {
        foreach ($recallsAll[$k] as $recallAll)
        {
            $idAll[] = $recallsAll[$k]->id;
            break;
        }
    }

    if(!empty($request->get('NEXT')))
    {
        $idAll = array_combine(array_merge(array_slice(array_keys($idAll), 1), array(count($idAll))), array_values($idAll));

        $keyCurrent = array_search("$id",$idAll);
        $keyCurrent++;
        $idNext = $idAll[$keyCurrent];
        if(array_search("$idNext",$idAll) == false)
        {
            return $app->redirect($app["url_generator"]->generate("404"));
        }

        return $app->redirect("/moreRecall/$idNext");

    }

    if(!empty($request->get('EARLY')))
    {
        $idAll = array_combine(array_merge(array_slice(array_keys($idAll), 1), array(count($idAll))), array_values($idAll));

        $keyCurrent = array_search("$id",$idAll);
        $keyCurrent--;
        $idEarly = $idAll[$keyCurrent];
        if(array_search("$idEarly",$idAll) == false)
        {
            return $app->redirect($app["url_generator"]->generate("404"));
        }

        return $app->redirect("/moreRecall/$idEarly");

    }




    $recalls = $app['db']->prepare("SELECT * FROM recalls WHERE id = $id");
    $recalls->execute();
    $recalls = $recalls->fetchAll(\PDO::FETCH_CLASS, \AI\Models\Recall::class);

    foreach ($recalls[0] as $recall)
    {
        $id = $recalls[0]->id;
        $author = $recalls[0]->author;
        $text = $recalls[0]->text;
        $created_at = $recalls[0]->created_at;
        $likes = $recalls[0]->likes;

        $results[] = [
            'id' => $id,
            'author' => $author,
            'text' => $text,
            'created_at' => $created_at,
            'likes' => $likes,
        ];
        break;
    }

    return $app['twig']->render('moreRecall.html.twig', array('results' => $results));
})->bind('MoreRecall');




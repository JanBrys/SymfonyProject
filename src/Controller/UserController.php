<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends AbstractController
{
	public function index(SessionInterface $session): Response
	{
		$client = HTTPClient::create();
		$url = "https://gorest.co.in/public-api/users";
		$request = $client->request("GET",$url);

		if($request->getStatusCode()===200)
		{
			$content = $request->toArray();
			$session->set("users",$content["data"]);

			return $this->render('/user.html.twig',[
				'users' => $session->get("users")
				]);
		}
		throw new HttpException(400, "Problem with request.");
	}

	public function ajax(SessionInterface $session,Request $request)
	{
		if($request->isXmlHttpRequest())
		{
			$users = $session->get("users");
			for($i=0;$i<count($users);$i++)
			{
				if($users[$i]["id"]==$request->get('id'))
				{
					$users[$i]["name"] = $request->get('name');
					$users[$i]["email"] = $request->get('email');
					$users[$i]["gender"] = $request->get('gender');
					$users[$i]["status"] = $request->get('status');
					$users[$i]["created_at"] = $request->get('created');
					$users[$i]["updated_at"] = $request->get('updated');
					$updatedUser = $users[$i];
				}
			}
			return new JsonResponse($updatedUser);
		}
		throw new HttpException(400, "Update user is not valid.");
	}

}
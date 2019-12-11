<?php
namespace Api\Controller;

use Slim\Http\UploadedFile;

class ImageController extends AbstractController
{
    public function imageUploadAction($request, $response, array $args)
    {
        $codeOrg = $args['codeOrg'];
        $path = ROOT_DIR.'/logs/'.$codeOrg;
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = array_values($uploadedFiles)[0];
        if (is_null($uploadedFile)) {
            return $response->withJson(array('err'=>'not found'))->withStatus(200);
        }
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            if (is_dir($path) === false) {
                mkdir($path);
            }
            $uploadedFile->moveTo($path.'/'.$uploadedFile->getClientFilename());
        } else {
            return $response->withJson(array('err'=>'file can not be found'))->withStatus(200);
        }

        return $response->withStatus(200)->withJson(array('msg'=>'image saved'));
    }
    public function imageFaceSaveAction()
    {
        $codeOrg = $args['codeOrg'];
        $path = ROOT_DIR.'/logs/'.$codeOrg;
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = array_values($uploadedFiles)[0];
        if (is_null($uploadedFile)) {
            return $response->withJson(array('err'=>'not found'))->withStatus(200);
        }
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            if (is_dir($path) === false) {
                mkdir($path);
            }
            $uploadedFile->moveTo($path.'/'.$uploadedFile->getClientFilename());
            
        } else {
            return $response->withJson(array('err'=>'file can not be found'))->withStatus(200);
        }

        return $response->withStatus(200)->withJson(array('msg'=>'image saved'));
    }


    public function imageCheckAction($request, $response, array $args)
    {
        $codeOrg = $args['codeOrg'];
        $users = array_diff(scandir(ROOT_DIR.'/logs/'.$codeOrg), array('..','.'));
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = array_values($uploadedFiles)[0];
        if (is_null($uploadedFile)) {
            return $response->withJson(array('err'=>'not found'))->withStatus(200);
        }
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $fr = $this->container->fr;
            $unknownUser = $this->moveUploadedFile($uploadedFile);
            $tempUser = $unknownUser;
            $unknownUser = $fr->imageRead('/logs/'.$unknownUser);
            $matches = [];
            foreach ($users as $key => $user) {
                $id = $user;
                $user = '/logs/'.$codeOrg.'/'.$user;
                $user = \CV\imread(ROOT_DIR.$user);
                $match = $fr->recognizeFaceLbph($unknownUser, $user);
                $matches +=[$match => $id];
            }
          
            unlink(ROOT_DIR.'/logs/'.$tempUser);
            unset($matches[""]);
            $name = min(array_keys($matches));
            if (is_null($name)) {
                return $response->withJson(array('err'=>'not found'))->withStatus(200);
            }
            if ($name > 31) {
                return $response->withJson(array('err'=>'not found'))->withStatus(200);
            }
            $name= $matches[$name];
        }
        // return $response->withStatus(200)->withJson(array($matches));
        return $response->withStatus(200)->withJson(array('user'=>$name));
    }
    public function showUserAction($request, $response, array $args)
    {
        $image = file_get_contents(ROOT_DIR.'/logs/'.$args['codeOrg'].'/'.$args['userId']);
        if ($image === false) {
            return $response->withStatus('200')->withJson(array('err'=>'not found'));
        }
        $response->write($image);
        return $response->withHeader('Content-Type', 'image/jpeg');
    }
    public function showUsersAction($request, $response, array $args)
    {
        $codeOrg = $args['codeOrg'];
        $users = array_diff(scandir(ROOT_DIR.'/logs/'.$codeOrg), array('..','.'));
        return $response->withJson($users)->withStatus(200);
    }
    public function imageDeleteAction($request, $response, array $args)
    {
        unlink(ROOT_DIR.'/logs/'.$args['codeOrg'].'/'.$args['userId']);
        return $response->withJson(array('msg'=>'user deleted succesfuly'))->withStatus(200);
    }
    // public function createOrganizationAction($request, $response, array $args)
    // {
    //     $organization = new \Api\Model\Organization($this->container->mongo);
    //     $result = $organization->saveOrganization($args);
    //     return $response->withJson($result->getInsertedId());
    // }
    
    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string $directory directory to which the file is moved
     * @param UploadedFile $uploaded file uploaded file to move
     * @return string filename of moved file
     */
    private function moveUploadedFile(UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo(ROOT_DIR.'/logs' . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
}

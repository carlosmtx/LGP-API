lgp
===

A Symfony project created on March 7, 2015, 7:41 pm.


Available Routes

- /channel/list
 
  Parameters: none
  
  Lists all the available channels

- /channel/create
   
  Parameters[POST]: name(string)
  
  Creates a new Channel named 'name'
   
- /channel/delete

  Parameters[POST]:  

  Deletes the Channel  by id  

channel_list_files:
  path: /channel/list/files
  defaults: { _controller: AppBundle:Channel:listFiles }



version_create:
  path: /version/create
  defaults: { _controller: AppBundle:Version:create }
version_delete:
  path: /version/delete
  defaults: { _controller: AppBundle:Version:delete }
version_current:
  path: /version/current/set
  defaults: { _controller: AppBundle:Version:setCurrent }

create_file:
  path: /file/create
  defaults: { _controller: AppBundle:File:addFile }
get_file:
  path: /file
  defaults: { _controller: AppBundle:File:getFile }

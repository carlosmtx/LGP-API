Available Routes

- **/channel/list
 
  Lists all the available channels

  Parameters: none
  
- /channel/create
 
  Creates a new Channel named 'name'
   
  Parameters[POST]
    
    name: the name of the new channel
   
- /channel/delete
 
  Deletes the Channel by it's 'id'  
  
  Parameters[POST]
  
     id: The id of the channel to be deleted  
  

- /channel/list/files
    
  Lists the files in the current version of channel 
  
  Parameters[POST] 
    channel : the id of the channel

- /version/create

 Creates a version associated with a channel
  
 Parameters:[POST]
     channel: the id of the channel
     name   : the name of the version that is going to be created

- /version/delete

    Deletes a version
    
    Parameters:[POST]
      version: the version to be deleted
     
- /version/current/set

  Sets the version as the channel current version

  Parameters:[POST]
    version: version
    
- /file/create

   Creates a file 

   Parameters:[POST]
     version: id da versao à qual o ficheiro vai ser adicionado
     file: ficheiro a ser submetido
- /file

   Returns the file with by it's id
   
   Parameters[GET]
    file: file id 
  

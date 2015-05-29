## Dependecies
- zip
- php5 ( > 5.5)
- php mongodb extension
- php curl extension
## Available Routes
All the parameteres enclosed between {} are variable

- /channels [GET]

        Lists all channels
        Parameters:
            none
        
- /channel/ [POST] 

        Creates a new channel with name 'channel'. 
        Parameters:
            
            channel : String | [obligatory] -- string with the channel name
            description: String | [optional] -- the channel description
            
        
- /channel/{cname} [DELETE]
        
        Deletes the channel 'cname'
        Parameters:
            none

- /channel/{cname}/current [GET]
        
        Returns the current version for the channel 'cname'.
        Note: By default a zip file is returned but if the get parameter 'as' is specified the version will be returned with the specified format(json)
        Parameters:
            as : [json,zip]  | [optional] the desired data format type 
        

- /channel/{cname}/current [POST]
        
        Sets the current version for the channel 'cname'.
        Parameters:
            scene : String | the id of the desired scene
        
        
- /channel/{cname}/scenes [GET]

        Lists all the existing scenes for the channel {cname}
        
        Parameters:
            none

- /channel/{cname}/scene [POST]

        Creates a scene associated with the channel cname
        
        Parameters:
        
        file : File   | [Obligatory] the .zip file containing the scene
        
        name : String | [Obligatory] the desired scene name

        description: String | [Optional] a description of the scene

- /channel/{cname}/scene [POST]

        Deletes a scene associated with the channel cname
        
        Parameters:
        
        scene : String | [Obligatory] the scene id to be deleted
        
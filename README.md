## Dependecies
- zip
- php5
- php5-mongo

## Available Routes
All the parameteres enclosed between {} are variable

- /channel/{cname} [GET]

        Returns all of the channel information
        
- /channel/{cname} [PUT]

        Creates a new channel with name 'cname'
        
- /channel/{cname} [DELETE]
        
        Deletes the channel 'cname'
        Note: all versions and files of this channel will also be deleted

- /channel/{cname}/current [GET]
        
        Returns the current version for the channel 'cname'.
        Note: if a get variable 'as' is specified the version will be returned with the specified extension(only zip is implemented by now)
        Ex: /channel/{cname}/current?as=zip

- /channel/{cname}/version [GET]

        Lists all the existing versions of the channel {cname}

- /channel/{cname}/current/{vname} [PUT,POST]

        Sets the version 'vname' as the current version of the channel 'cname'

- channel/{cname}/version/{vname} [PUT]

        Creates the version 'vname' inside the channel 'cname'

- channel/{cname}/version/{vname} [DELETED]

        Deletes the version 'vname' inside the channel 'cname'
        Note: all files inside the version will also be deleted


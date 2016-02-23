# elastic-erga
A toolkit for elastic search. 

We are aiming to create a simple toolkit which will allow users to monitor various elastic search servers & indeces for user 
configurable conditions and alert if they are met.

The application is used entirely via a web UI 



# Contribute

## Quick Start
    
### setup homestead
    
    composer install
    php vendor/bin/homestead make
    vagrant up
    
### enter the box
    
    vagrant ssh

### build front-end
   
    npm i
    gulp

### migrate the tables

    php arisan migrate:refresh --seed
    


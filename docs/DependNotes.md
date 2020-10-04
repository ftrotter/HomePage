## Totem erros
If you see an error like: 

 Class 'Studio\Totem\Providers\TotemServiceProvider' not found

It is because of a previous depenancy for our Laravel stack that has been removed and refactored. 

### To fix this..
You need to delete the line: 

 Studio\Totem\Providers\TotemServiceProvider::class,

from your ./config/app.php file

## Fideloper errors

fix like this: 

https://github.com/CareSet/EBB_PECOS/commit/0fff7a9e4157a823d268d47d4d3762238bcb7ee4

* remove TrustProxies line from app/Http/Kernel.php
* remove fideloper from composer.json

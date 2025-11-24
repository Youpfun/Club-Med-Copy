<!DOCTYPE html> <html lang="fr"> <head> <meta charset="UTF-8"> 
    <title>Liste des Clients</title> <style>
        /* Un peu de style pour que ce soit lisible */ table { width: 
        100%; border-collapse: collapse; margin-top: 20px; font-family: 
        Arial, sans-serif; } th { background-color: #007BFF; color: 
        white; padding: 10px; text-align: left; } td { border: 1px solid 
        #ddd; padding: 8px; }
        tr:nth-child(even) { background-color: #f2f2f2; } </style> 
</head> <body>
    <h1>Annuaire des Clients</h1> <table> <thead> <tr> <th>N° 
                Client</th> <th>Nom & Prénom</th> <th>Login</th> 
                <th>Email</th> <th>Téléphone</th> <th>Ville</th> 
                <th>Date Naissance</th>
            </tr> </thead> <tbody> <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <tr> 
                    <td><?php echo e($client->numclient); ?></td>
                    
                    <td> <?php echo e($client->nomclient); ?> <?php echo e($client->prenomclient); ?> <br> <small>(<?php echo e($client->genreclient); ?>)</small>
                    </td> <td><?php echo e($client->login); ?></td> <td><?php echo e($client->emailclient); ?></td> <td><?php echo e($client->telephone); ?></td>
                    
                    <td> <?php echo e($client->numrue); ?> <?php echo e($client->nomrue); ?><br> 
                        <?php echo e($client->codepostal); ?> <strong><?php echo e($client->ville); ?></strong>
                    </td> <td><?php echo e($client->datenaissance); ?></td> </tr> 
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody> </table> </body>
</html>
<?php /**PATH /home/s324-clubmed/club-med/ClubMed/resources/views/clients.blade.php ENDPATH**/ ?>
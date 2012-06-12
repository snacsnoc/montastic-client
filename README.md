Montastic client
================

#### Intro
I've been using Montastic for years now, and am quite impressed about their service. So, here's a class to help interface with their API.


#### Example: get all monitored websites (or checkpoints)
    <?php
    require './Montastic.php';

    $montastic = new Montastic('email@address.com', 'password');
    
    $all = $montastic->getAllCheckpoints();

    //Loop through all checkpoint names
    foreach ($all->checkpoint as $i => $value) {
        echo $value->name;
        echo '<br>';
    }

#### Example: Get info about a specific checkpoint
    $montastic->getCheckpoint(31337)->notes;


#### Example: Delete a checkpoint
    $montastic->deleteCheckpoint(31337);

#### Example: Update a checkpoint's data
    $montastic->updateCheckpoint(31337, 'notes', 'updated note!');

#### Example: Create a new checkpoint
    $montastic->createCheckpoint('http://kernel.org');
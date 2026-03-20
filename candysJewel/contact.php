 <?php

 include 'header.php';

 ?>


 <h2> Contact</h2>


    <div class="container" >
        <form action="contact.php" method="post" id="formulaire">
            <fieldset>
                <legend></legend>
                <div id="nomLabel" class="input-container">
                    <label for="nom">Nom:</label>
                    <input type="text"
                    name="nom"
                    id="nom"
                    class="nom">
                    <span id="erreurNom" class="erreur"></span>
                </div>
                <div id="emailLabel" class="input-container">
                    <label for="email">Adresse email:</label>
                    <input type="text"
                    name="email"
                    id="email"
                    class="email">
                    <span id="erreurEmail" class="erreur"></span>
                </div>



            </fieldset>

            <fieldset>
                <legend></legend>
                <div class="input-container">
                    <label for="text-area"></label>
                    <textarea name="contenu" id="text-area" cols="40" rows="20" placeholder="Ecrivez des commentaires"></textarea>
                </div>
            </fieldset>

            <fieldset>
                <legend></legend>
                <div class="input-container">
                    <label for="text">Type bijoux: </label>
                    <select name="text" id="text">
                       <option value="homme">Hommes</option>
                        <option value="femme">Femmes</option>
                        <option value="enfant">Enfants</option>
                    </select>

                </div>
                <div class="input-container">
                    <input type="submit" value="Envoyer">
                </div>
            </fieldset>
        </form>
    </div>
    <div class=" container"></div>
<?php include 'footer.php'; ?>

<!-- Avis -->
<div class="review">
    <div class="review-main">
        <div class="review-header">
            <img src="<?php echo htmlspecialchars($review['photo']); ?>" alt="" class="review-img">
            <div class="review-name">
                <h3>
                    <?php 
                        echo htmlspecialchars($review['name']).' '.
                        htmlspecialchars($review['surname'][0].'.');
                    ?>
                </h3>
                <div class="review-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa-solid fa-star <?= $i <= (int)$review['score'] ? 'score' : '' ?>"></i>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <div class="review-message">
            <p>
                <?php echo htmlspecialchars($review['message']); ?>
            </p>
        </div>
    </div>
    <div class="review-footer">
        <p>
            <?php
                $date = new DateTime($review['review_date']);

                $months = [
                    1 => 'janvier',
                    2 => 'février',
                    3 => 'mars',
                    4 => 'avril',
                    5 => 'mai',
                    6 => 'juin',
                    7 => 'juillet',
                    8 => 'août',
                    9 => 'septembre',
                    10 => 'octobre',
                    11 => 'novembre',
                    12 => 'décembre'
                ];

                echo $date->format('j') . ' ' .
                    $months[(int)$date->format('n')] . ' ' .
                    $date->format('Y');
            ?>    
        </p>
        <!-- <a href="#" class="btn body-btn">
            12
            <i class="fa-regular fa-heart"></i>
        </a> -->
    </div>
</div>
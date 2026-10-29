<style>
    .running-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08); /* Premium shadow */
        padding: 60px;
        border: none;
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .timer-display {
        background: linear-gradient(87deg, #172b4d 0, #1a174d 100%);
        color: #fff;
        font-family: 'Courier New', Courier, monospace;
        font-size: 60px;
        font-weight: 700;
        padding: 20px 40px;
        border-radius: 15px;
        display: inline-block;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        margin: 30px 0;
        letter-spacing: 5px;
    }
    .client-badge {
        background: #f6f9fc;
        color: #8898aa;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        display: inline-block;
    }
    .btn-end-meeting {
        background: linear-gradient(87deg, #f5365c 0, #f56036 100%);
        border: none;
        border-radius: 30px;
        padding: 15px 50px;
        font-size: 18px;
        font-weight: 700;
        color: white;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11);
        transition: all 0.3s;
    }
    .btn-end-meeting:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1);
        color: white;
    }
    .pulse-dot {
        height: 12px;
        width: 12px;
        background-color: #f5365c;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        animation: blink 1s infinite;
    }
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }
</style>

<div class="running-card">
    <div class="client-badge"><i class="fa fa-user"></i> <?php echo $meeting->client_name; ?></div>
    
    <h2 style="font-weight: 800; color: #32325d; margin-top: 10px;">
        <span class="pulse-dot"></span> Meeting in Progress
    </h2>
    
    <div>
        <div class="timer-display">
            <span id="timer">00:00:00</span>
        </div>
    </div>
    
    <p class="text-muted" style="font-size: 16px;">Started at <?php echo date('h:i A', strtotime($meeting->start_time)); ?></p>

    <form action="<?php echo site_url('new_movement/end_meeting'); ?>" method="post" class="mt-4">
        <button type="submit" class="btn btn-end-meeting">
            <i class="fa fa-stop-circle-o"></i> END MEETING
        </button>
    </form>
</div>

<script>
// Simple Timer
var startTime = new Date("<?php echo $meeting->start_time; ?>").getTime();

setInterval(function() {
    var now = new Date().getTime();
    var distance = now - startTime;
    
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    document.getElementById("timer").innerHTML = 
    (hours < 10 ? "0" + hours : hours) + ":" + 
    (minutes < 10 ? "0" + minutes : minutes) + ":" + 
    (seconds < 10 ? "0" + seconds : seconds);
}, 1000);
</script>

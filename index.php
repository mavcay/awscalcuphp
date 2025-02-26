<?php
// Express.js API URL
$api_url = "http://localhost:5000/api"; 

// Fetch data from API
$countryData = json_decode(file_get_contents("$api_url/countries"), true);
$serviceData = json_decode(file_get_contents("$api_url/services"), true);
$callTypeData = json_decode(file_get_contents("$api_url/call-types"), true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>AWS Cost Calculation</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #007BFF; color: white; }
        .total-cost { font-weight: bold; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <h2>AWS Calculation Results</h2>

    <form id="calculationForm">
        <label for="country">Country:</label>
        <select id="country" name="country_id">
            <?php foreach ($countryData as $row): ?>
                <option value="<?= $row['country_id']; ?>"><?= $row['country_name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="service">Service:</label>
        <select id="service" name="service_id">
            <?php foreach ($serviceData as $row): ?>
                <option value="<?= $row['service_id']; ?>"><?= $row['service_name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="call_type">Call Type:</label>
        <select id="call_type" name="call_type_id">
            <?php foreach ($callTypeData as $row): ?>
                <option value="<?= $row['call_type_id']; ?>"><?= $row['call_type_name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="daily_usage">Daily Usage:</label>
        <input type="number" id="daily_usage" name="daily_usage" value="15" required>

        <label for="business_days">Business Days:</label>
        <input type="number" id="business_days" name="business_days" value="30" required>

        <label for="aht">AHT (Avg Handle Time in Minutes):</label>
        <input type="number" step="0.01" id="aht" name="aht" value="5.00" required>

        <button type="submit">Calculate</button>
    </form>

    <h3>Calculation Results</h3>

    <table id="resultsTable" style="display: none;">
        <thead>
            <tr>
                <th></th>
                <th>Country</th>
                <th>Daily</th>
                <th>Business Days</th>
                <th>Monthly Calls</th>
                <th>AHT (mins)</th>
                <th>Call Mins</th>
                <th>Cost per Min</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cost (Voice Usage)</td>
                <td id="country_name"></td>
                <td id="daily_usage_value"></td>
                <td id="business_days_value"></td>
                <td id="monthly_calls"></td>
                <td id="aht_value"></td>
                <td id="call_minutes"></td>
                <td id="cost_voice_usage"></td>
                <td id="total_cost"></td>
            </tr>
        </tbody>
    </table>

    <script>
        $("#calculationForm").submit(function(e) {
            e.preventDefault(); 

            $.post("calculate.php", $(this).serialize(), function(response) {
                $("#resultsTable").show();

                $("#country_name").text("Selected Country");
                $("#daily_usage_value").text(response.daily_usage);
                $("#business_days_value").text(response.business_days);
                $("#aht_value").text(response.aht);
                $("#monthly_calls").text(response.monthly_calls);
                $("#call_minutes").text(response.call_minutes);

                $("#cost_voice_usage").text("$" + parseFloat(response.cost_voice_usage).toFixed(4));
                $("#total_cost").text("$" + parseFloat(response.total_cost).toFixed(4));
            }, "json");
        });
    </script>
</body>
</html>

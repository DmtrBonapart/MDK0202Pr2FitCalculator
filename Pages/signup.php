<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCalculator - Регистрация</title>
    <link rel="stylesheet" href="../Style/Global/variables.css">
    <link rel="stylesheet" href="../Style/Global/fonts.css">
    <link rel="stylesheet" href="../Style/Global/base.css">
    <link rel="stylesheet" href="../Style/UI/buttons.css">
    <link rel="stylesheet" href="../Style/Layouts/header.css">
    <link rel="stylesheet" href="../Style/Layouts/footer.css">
    <link rel="stylesheet" href="../Style/Components/signup-form.css">
    <script src="../Components/header.js"></script>
    <script src="../Components/footer.js"></script>
    <script src="../Components/signup-form.js"></script>
    <style>
        main {
            min-height: calc(100vh - 64px - 120px);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 120px 0;
        }
    </style>
</head>
<body>
    <my-header></my-header>
    
    <main>
        <signup-form></signup-form>
    </main>
    
    <my-footer></my-footer>
</body>
</html>
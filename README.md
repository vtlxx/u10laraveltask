<h1>Simple API for delivery order</h1>
<h2>API Endpoints:</h2>
<ul>
    <li>
        <code><b>POST</b></code> <code><i>/delivery</i></code>
        Creating new delivery order for package
        <br/>
        <h5>Request POST fields:</h5>
        <ul>
            <li><code>package_width</code> - non-negative number</li>
            <li><code>package_height</code> - non-negative number</li>
            <li><code>package_length</code> - non-negative number</li>
            <li><code>package_weight</code> - non-negative number</li>
            <li><code>customer_full_name</code> - string, from 3 to 128 characters</li>
            <li><code>customer_phone_number</code> - string, from 10 to 13 characters</li>
            <li><code>customer_email</code> - string</li>
            <li><code>customer_address</code> - string, from 3 to 256 characters</li>
        </ul>
        <h5>Response (JSON encoded):</h5>
        <ul>
            <li><code>success</code> - boolean, true/false</li>
            <li><code>id</code> - number, unique id of the package in database</li>
            <li><code>ttn</code> - string, unique id of the package in delivery company</li>
        </ul>
    </li>
    <br/>
    <br/>
    <li>
    <code><b>GET</b></code> <code><i>/delivery/{id}</i></code>
    Info about delivery by its ID
    <br/>
    <h5>Response (JSON encoded):</h5>
    <ul>
        <li><code>success</code> - boolean, true/false</li>
        <li>
            <code>result</code> - array, info about delivery
            <br/>
            Fields: "ttn", "height", "width", "length", "weight", "full_name", "phone_number", "email", "address".
        </li>
    </ul>
    <br/>
    <br/>
    <li>
    <code><b>GET</b></code> <code><i>/delivery</i></code>
    List all deliveries by client's phone number
    <br/>
    <h5>Required GET parameters:</h5>
    <ul>
        <li><code>phone_number</code> - string, from 10 to 13, phone number of existing client</li>
    </ul>
    <h5>Response (JSON encoded):</h5>
    <ul>
        <li><code>success</code> - boolean, true/false</li>
        <li>
            <code>result</code> - array, info about delivery
            <br/>
            Fields: "ttn", "height", "width", "length", "weight", "full_name", "phone_number", "email", "address"
        </li>
    </ul>
</li>
</ul>
<hr/>
<h2>Будущие доработки:</h2>
1. Клиенту может понадобиться сортировать посылки по дате создания. В таком случае можно будет добавить возможность получения всех посылок, с фильтром через GET-запрос.
<br/><br/>
2. При реализации возможности отправки несколькими службами доставки, нужно сделать следующее:<br/>
1) Добавить таблицу delivery_companies (id, name)<br/>
2) При запросе на создание посылки, должен указываться id службы доставки<br/>
3) Добавить switch по id службы доставки, перед отправкой запроса. В зависимости от выбранной службы, отправлять запросы на разные URL<br/>
4) В таблицу deliveries добавить колонку delivery_service (закомментировано в миграции)<br/>
<br/><br/>
3. При большом количестве служб доставки можно сделать основной контроллер DeliveryController, от него наследовать контроллеры для каждой службы доставки (NovaPoshtaDeliveryController, JustinDeliveryController) и вынести отправку запроса в отдельную функцию. В дочерних контроллерах должна переназначаться только функция отправки запроса в службу доставки и возвращать ТТН посылки.
<br/><br/>
4.<br/>
1) Проверить проблему на стороне клиента (отправляется ли запрос к нам на сервер, через инспектор->network)<br/>
2) Проверить приходит ли запрос к нам на сервер (+ глянуть логи)<br/>
3) Проверить работает ли оформление для других служб доставки<br/>
4) Проверить работает ли API службы доставки<br/>
Если проблему локализовать не удалось, то углубляться и искать проблему в коде.

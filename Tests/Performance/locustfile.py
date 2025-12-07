from locust import HttpUser, task, between

class WebsiteUser(HttpUser):
    wait_time = between(1, 3)

    @task
    def login_and_add_cart(self):
        # Test login
        self.client.post("/Controller/Login_Process.php", {
            "email": "test@example.com",
            "password": "123456",
            "login": "1"
        })

        # Test add to cart
        self.client.post("/Controller/Cart_Process.php", {
            "user_id": 2,
            "product_id": 2,
            "quantity": 1,
            "size": "M"
        })

package main

import (
	"encoding/json"
	"errors"
	"fmt"
	"log"

	"github.com/streadway/amqp"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

//Message type
type Message struct {
	BulidNo int

	Username string
	Password string
}

//Building ...
type Building struct {
	Building int `gorm:"column:building"`
	Floor    int `gorm:"column:floor"`
	Room     int `gorm:"primaryKey"`
	Occupied int `gorm:"column:occupied"`
}

//Error ...
type Error struct {
	Room   int    `gorm:"column:room"`
	Error  string `gorm:"column:error"`
	People int    `gorm:"column:people"`
}

//Roleaccess ...
type Roleaccess struct {
	Access     string `gorm:"primaryKey"`
	Admin      int    `gorm:"column:admin"`
	Superadmin int    `gorm:"column:superadmin"`
	Student    int    `gorm:"column:student"`
}

//Userinfo ...
type Userinfo struct {
	Username string `gorm:"primaryKey"`
	Password string `gorm:"column:password"`
	Userrole string `gorm:"column:userrole"`
	Room     int    `gorm:"column:room"`
}

//Actroom ...
type Actroom struct {
	Roomid    int    `gorm:"column:roomid"`
	Time      int    `gorm:"column:time"`
	Username1 string `gorm:"column:username1"`
	Username2 string `gorm:"column:username2"`
	Occupied  int    `gorm:"column:Occupied"`
}

func failOnError(err error, msg string) {
	if err != nil {
		log.Fatalf("%s: %s", msg, err)
	}
}

func main() {
	conn, err := amqp.Dial("amqp://guest:guest@47.97.108.162:5672/")
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"hello", // name
		false,   // durable
		false,   // delete when unused
		false,   // exclusive
		false,   // no-wait
		nil,     // arguments
	)
	failOnError(err, "Failed to declare a queue")

	msgs, err := ch.Consume(
		q.Name, // queue
		"",     // consumer
		true,   // auto-ack
		false,  // exclusive
		false,  // no-local
		false,  // no-wait
		nil,    // args
	)
	failOnError(err, "Failed to register a consumer")

	forever := make(chan bool)

	go func() {
		//消息处理
		//连接数据库
		dsn := "web2021:web2021@tcp(localhost:3306)/web2021?charset=utf8mb4&parseTime=True&loc=Local"
		db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})
		if err != nil {
			fmt.Println(err)
		}
		fmt.Printf("ddd")
		fmt.Println("------------连接数据库成功-----------")

		//处理业务
		for d := range msgs {
			data := d.Body
			var k Message
			json.Unmarshal(data, &k)
			buildNo := k.BulidNo
			//username := k.Username
			//password := k.Password

			//顺序查找第一个合适的宿舍，重复申请宿舍已在前端拦截
			building := Building{}
			errBuilding := db.Table("building").Where(" building = ?", buildNo).Where("occupied < 4").First(&building).Error
			if errors.Is(errBuilding, gorm.ErrRecordNotFound) {
				fmt.Println("该楼不满足新生住宿申请条件")
			} else {

				errRoomAlloc := db.Table("building").Where("room = ?", building.Room).Update("occupied", building.Occupied+1).Error
				if errRoomAlloc != nil {
					fmt.Println("分配宿舍时失败")
				} else {
					fmt.Printf("成功申请宿舍：%d", building.Room)
				}
				errStuChange := db.Table("userinfo").Where()
			}

		}

	}()

	log.Printf(" [*] Waiting for messages. To exit press CTRL+C")
	<-forever
}

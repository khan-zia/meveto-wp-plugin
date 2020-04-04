##Description
Meveto is a cyber-security company based in California, US. We aim to provide strong, decentralized and simple authentication system that can easily replace the current outdated and obsolete passwords based or 2FA and MFA based authentication systems that can be compromised easily. With Meveto, The need for passwords and other weak measures is completely eliminated. Meveto empowers your personal mobile devices such as your phone, to always be able to authenticate you everywhere over the internet (of course you can only use Meveto with services that have adopted it) and we hope that soon we will be revolutionizing the way not only people, but workstations, devices and IoT authenticate.

##How it works?
Meveto uses PKI (Public Key Infrastructure) to authenticate an entity. It uses curve X25519 of the strong elliptic curves cryptography with a key size of 384 bytes. When a device is paired with a Meveto account, a public private key pair is generated on the device itself and the public key is sent to the Meveto servers. This way, the private key, which is the most important piece in the puzzle, never ever leaves the original device thus ensuring maximum security. Each device generates a new pair through Meveto app (Android or iOS) when it's being paired (even if the app is re-installed on the same device) and this way the entire security control is full decentralized. Here are the steps that briefly explains the way Meveto works even further.

###The Registration
- A user registers with Meveto. Meveto associates sends the user a "device ID" and a short, one time "Pairing Key".
- The user downloads Meveto app on their device. They  use the Device ID and Pairing Password to pair the device with their Meveto account.
- Meveto app before sending the "pairing request", generates a public and private key pair. It also then sends the public key along with the device ID and pairing key to the Meveto servers.
- Meveto servers verifies device ID and pairing key and stores the public key of the device.

###The Authentication
- From Meveto's website, user enters their username or email address and requests login.
- Meveto generates up to 6 pseudo random digits and displays it to the user on their screen. We call this a session ID, however, it has absolutely no significant role to play and does not need to be unique or something. Additionally, Meveto also sends a "LoginSessionToken" to the browser that the browser can then exchange for an authentication token when the process is complete.
- Users enter the session ID digits they see on the screen into their paired Meveto app and presses the "Authenticate" button.
- Meveto app sends its ID (which was stored at the time of initial pairing), the session digits that the user just entered and then signs the request with its private key.
- Meveto servers first validates the input data of course, then uses the "Device ID" to fetch the "Public key" of the device that was stored during the pairing process. Then Meveto servers uses the public key to verify the signature of the request. If the signature is successfully verified, then Meveto checks the Session digits and verifies those as well (Note that here the verification of those session digits is only needed to confirm that the user has actually requested a login) otherwise, the authentication is done through the verification of the signature.
- If all goes well, Meveto servers broadcasts an event that the authentication has been successful. The user's browsers listens to the broadcast and then requests an Authentication token from the servers against the "LoginSessionToken" which was received by the browser when the user requested login.

##Installation

To get started with using Meveto with your website, first you will need to [register with Meveto as a business](https://dashboard.meveto.com/register-main). After completing the registration, next you will need to [register your website with Meveto](https://dashboard.meveto.com/merchant-integration). The following information will be required for registering a WordPress based website.

###Application Type
Choose "WordPress" as your application type.

###Application Name
A short name of your website. Make sure your users can identify your website by this name.

###Description
A description of your website. What it does? who is it for?

###Web Address
This is very important for WordPress based website. This **must be the primary address of your website** without any parameters, subdomains or anything. However, if WordPress itself is deployed on a subdomain, then that's fine. In short, this address should be what the **home_url()** function in WordPress returns.

After you enter the web address, there are 3 more URLs (Login, Redirect and Webhook) that will be automatically populated for you based on your main web address. Submit this information and you are done. As soon as the registration is complete, Meveto will return a **client ID** and a **client secret** that you will need to save in a safe location. **You will never be able to see the client secret again** however, your client's (website's) ID will be visible on your dashboard. Once the registration process is complete, Next you will need to setup your WordPress website.

- Install and activate the plugin through the WordPress plugins screen directly or if you have a zip file of it, then you can simply upload it.
- When activated, the plugin will add a "Meveto" to the sidebar menu of the dashboard. This is where you will be able to complete the configuration.
- Use the **client ID** and **client secret** you got from Meveto and leave rest of the configuration values as per default values.

To ensure seamless user experience, you will also need to use a websocket service. Meveto plugin at the moment only supports [pusher](https://pusher.com). After registering with pusher, you will need to get the required 4 values from pusher and configure pusher for Meveto at the Meveto settings page.

You are all set. You and your users should now be able to login to your website using Meveto without using any passwords and always having your device be in total control. For more information on Meveto, [visit our website](https://meveto.com) or for [visit our documentation](https://developers.meveto.com) for technical details.


##Frequently Asked Questions

###What happens if I lose my phone? How do I login to the website?

When you register with Meveto, you will also get a random key that we call "fixcode". In case you lose your paired device, you can use your fixcode along with other required personal information to get access to your account and be able to revoke access for your lost phone immediately or add a new device and pair it. When you use a fixcode, you will get a new one immediately and you must always ensure that you never lose your current fixcode.

###Does Meveto collect any data on my website?

No, Meveto does not collect any kind of data on your website.

###I can no longer login to the website using my password. Why is that?

By default, Meveto plugin will disable password-based logins for all of your users and admins. The purpose of Meveto is to eliminate passwords and hence boost your security. It does not make your website any secure at all if passwords are still active. Your website is only as much secure as the weakest point it has. If passwords are not disabled, then Meveto will not be able to prevent any one from using a compromised password. However, in the Meveto settings page, you can choose to allow passwords. But this should never be a permanent option and must only be used in special circumstances where you really need to temporarily allow passwords to work.

###How does Meveto disable passwords? Does it delete something?

No, Meveto does not mess with the data of your website at all. Meveto disables passwords by simply intercepting any attempt to login to your website that's using a password and immediately logs the user back out even if the user credentials are correct.

###Not all of my website users are on Meveto, if Meveto disables password, then how can those users login to my website that don't use Meveto yet?

Great question, Meveto has absolutely thought of this. When Meveto intercepts a password-based login attempt, before logging the user out and rejecting the request, Meveto makes sure that the user has logged in to your website **at least once** using Meveto. When a user logs in to your website for the very first time, Meveto adds the user to its table in the database to keep track of the user. Next time when the same user tries to login with a password, their request will be rejected. So, those users that don't use Meveto, can still login to your website using their passwords.

##License
[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)